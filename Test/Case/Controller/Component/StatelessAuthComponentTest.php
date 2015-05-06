<?php
/**
 * StatelessAuthComponent file
 *
 * @package StatelessAuth.Test.Case.Controller.Component
 */
App::uses('Controller', 'Controller');
App::uses('StatelessAuthComponent', 'StatelessAuth.Controller/Component');

// test classes for mocking
App::import('Test', 'StatelessAuth.test_classes');

/**
 * StatelessAuthComponentTest class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class StatelessAuthComponentTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.stateless_auth.user',
	);

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		// Ensure all requests start with a valid token attached.
		$token = 'cc0a91cfa4f3b703531c1dc4f5f64b89';
		$request = $this->getMock('CakeRequest', array('header'), array(null, false));
		$request->staticExpects($this->any())
			->method('header')
			->with('Authorization')
			->will($this->returnValue('Bearer ' . $token));

		$this->Controller = new StatelessAuthController($request, $this->getMock('CakeResponse'));
		$this->Controller->request = $request;

		$collection = new ComponentCollection();
		$collection->init($this->Controller);
		$this->Component = new TestStatelessAuthComponent($collection);
		$this->Component->request = $request;
		$this->Component->response = $this->getMock('CakeResponse');

		$this->Controller->Components->init($this->Controller);
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Controller, $this->Component);
	}

	/**
	 * Set up mocked Auth embedded objects.
	 *
	 * Helper that sets up Auth's embedded authenticate and authorize
	 * objects to return true or false as specified. Call where you
	 * would normally call `$this->Component->initialize($this->Controller);`.
	 *
	 * @return void
	 */
	public function initComponentAuthObjects($authenticateResult = false, $authorizeResult = false) {
		$this->Component->initialize($this->Controller);
		// Set up fake auth objects to isolate tests to StatelessAuthComponent only.
		$authenticateMock = $this->getMock('TokenAuthenticate', array('authenticate'), array(), '', false);
		$authenticateMock->expects($this->any())
			->method('authenticate')
			->will($this->returnValue($authenticateResult));
		$this->Component->authenticateObject = $authenticateMock;

		$authorizeMock = $this->getMock('IsAuthorizedAuthorize', array('authorize'), array(), '', false);
		$authorizeMock->expects($this->any())
			->method('authorize')
			->will($this->returnValue($authorizeResult));
		$this->Component->authorizeObject = $authorizeMock;
	}

	/**
	 * Test initialize() success.
	 *
	 * @return void
	 */
	public function testInitializeSuccessful() {
		$controller = $this->getMock('StatelessAuthController', array(), array(), '', false);
		$controller->request = new StdClass();
		$controller->request->params = array('action' => 'canary', 'controller' => 'foo');
		$controller->response = 'toucan';

		$this->Component->initialize($controller);

		$this->assertEquals(
			$controller,
			$this->Component->controller,
			'Component should be initialized with the provided controller instance.'
		);
	}

	/**
	 * Test that startup() calls getUser() at the appropriate time.
	 *
	 * @return void
	 */
	public function testStartupGetUser() {
		// The call to authenticateObject->getUser() must fail.
		$authenticateMock = $this->getMock('TokenAuthenticate', array('getUser'), array(), '', false);
		$authenticateMock->expects($this->once())
			->method('getUser')
			->will($this->returnValue(false));
		$this->Component->authenticateObject = $authenticateMock;

		$this->Component->deny(); // Block all "public" access, enforce a user.
		$this->Controller->request['action'] = 'add'; // Must be a valid action.
		$this->Component->setUser(null); // There must not be a user available.

		$this->assertFalse(
			$this->Component->startup($this->Controller),
			'::startup() must return false when no user can be retrieved.'
		);
	}

	/**
	 * Test that startup() calls isAuthorized() at the appropriate time.
	 *
	 * @return void
	 */
	public function testStartupisAuthorized() {
		$this->initComponentAuthObjects(true, true);

		$this->Component->deny(); // Block all "public" access, enforce a user.
		$this->Controller->request['action'] = 'add'; // Must be a valid action.

		$this->assertTrue(
			$this->Component->startup($this->Controller),
			'::startup() must return true when user is authorized to access a protected method.'
		);
	}

	/**
	 * Test whichUser().
	 *
	 * @return void
	 */
	public function testWhichUser() {
		$this->assertEquals(
			null,
			$this->Component->whichUser(null),
			'Must return null when no user array is available.'
		);

		$user = array('canary');
		$this->Component->setUser($user);

		$this->assertEquals(
			$user,
			$this->Component->whichUser(null),
			'Must return the ::user property of the component when none is provided as an arg.'
		);

		$user = array('Subkey' => array('foo' => 'bar'));
		$this->assertEquals(
			$user,
			$this->Component->whichUser($user),
			'Must return the provided array when it is valid.'
		);
	}

	/**
	 * testUser method
	 *
	 * @return void
	 */
	public function testUser() {
		$data = array(
			'User' => array(
				'id' => '2',
				'username' => 'mark',
				'group_id' => 1,
				'Subkey' => array(
					'foo' => 'read',
					'bar' => 'write',
					'baz' => 'none',
				),
				'is_admin' => false,
		));
		$this->Component->setUser($data['User']);

		$result = $this->Component->user();
		$this->assertEquals($data['User'], $result);

		$result = $this->Component->user('username');
		$this->assertEquals($data['User']['username'], $result);

		$result = $this->Component->user('Subkey.foo');
		$this->assertEquals($data['User']['Subkey']['foo'], $result);

		$result = $this->Component->user('invalid');
		$this->assertEquals(null, $result);

		$result = $this->Component->user('Company.invalid');
		$this->assertEquals(null, $result);

		$result = $this->Component->user('is_admin');
		$this->assertFalse($result);
	}

	/**
	 * Test that login() delegates to the authentication object.
	 *
	 * @return void
	 */
	public function testLoginDeletatesToAuthenticate() {
		$user = array(
			'id' => 1,
			'username' => 'mark',
			'token' => 'sample-token',
		);
		$this->Component->setUser($user);
		$this->initComponentAuthObjects(true, true);
		$result = $this->Component->login();
		$this->assertTrue($result);
		$this->assertEquals($user, $this->Component->user());
	}

	/**
	 * Test that login() sets the ::user property when a new one is provided.
	 *
	 * @return void
	 */
	public function testLoginSetsProvidedUser() {
		$user = array(
			'id' => 1,
			'username' => 'mark',
		);
		$this->Component->setUser(null);
		$this->initComponentAuthObjects(true, true);
		$result = $this->Component->login($user);
		$this->assertTrue($result);
		$this->assertEquals($user, $this->Component->user());
	}

	/**
	 * Test that login() sets the ::user property when a new one is provided.
	 *
	 * @return void
	 */
	public function testLoginSetsProvidedUserWithToken() {
		$user = array(
			'id' => 1,
			'username' => 'mark',
			'token' => 'sample-token',
		);
		$this->Component->setUser(null);
		$this->initComponentAuthObjects(true, true);
		$result = $this->Component->login($user);
		$this->assertTrue($result);
		$this->assertEquals($user, $this->Component->user());
	}

	/**
	 * Test that identify returns false on a failure
	 *
	 * @return void
	 */
	public function testIdentifyReturnsFalse() {
		$this->initComponentAuthObjects(false, false);
		$result = $this->Component->identify($this->Component->request, $this->Component->response);
		$this->assertFalse($result);
	}

	/**
	 * Test that identify returns the user on success
	 *
	 * @return void
	 */
	public function testIdentifyReturnsUser() {
		$user = array(
			'id' => 1,
			'username' => 'mark',
			'token' => 'sample-token',
		);
		$this->initComponentAuthObjects($user, true);
		$result = $this->Component->identify($this->Component->request, $this->Component->response);
		$this->assertEquals($user, $result);
	}

	/**
	 * Test that logout() delegates to the authentication object.
	 *
	 * @return void
	 */
	public function testLogout() {
		// Inject a fake user into the Component.
		$user = array(
			'id' => 1,
			'username' => 'mark',
		);
		$this->Component->setUser($user);

		// Tell the authenticateObject to return a canary value.
		$expected = 'canary';
		$Authenticate = $this->getMock('TokenAuthenticate', array('logout'), array(), '', false);
		$Authenticate->expects($this->any())
			->method('logout')
			->with($user)
			->will($this->returnValue($expected));
		$this->Component->authenticateObject = $Authenticate;

		// Execute the call.
		$result = $this->Component->logout();

		// Confirm we recieved the canary value back.
		$this->assertEquals($expected, $result);
	}

	/**
	 * Tests that deny always takes precedence over allow
	 *
	 * @return void
	 */
	public function testAllowDenyAll() {
		// Set up fake auth objects to isolate tests to StatelessAuthComponent only.
		$this->initComponentAuthObjects();

		// Run the tests.
		$this->Component->allow();
		$this->Component->deny('add', 'camelCase');

		$this->Controller->request['action'] = 'delete';
		$this->assertTrue($this->Component->startup($this->Controller));

		$this->Controller->request['action'] = 'add';
		$this->assertFalse($this->Component->startup($this->Controller));

		$this->Controller->request['action'] = 'camelCase';
		$this->assertFalse($this->Component->startup($this->Controller));

		$this->Component->allow();
		$this->Component->deny(array('add', 'camelCase'));

		$this->Controller->request['action'] = 'delete';
		$this->assertTrue($this->Component->startup($this->Controller));

		$this->Controller->request['action'] = 'camelCase';
		$this->assertFalse($this->Component->startup($this->Controller));

		$this->Component->allow('*');
		$this->Component->deny();

		$this->Controller->request['action'] = 'camelCase';
		$this->assertFalse($this->Component->startup($this->Controller));

		$this->Controller->request['action'] = 'add';
		$this->assertFalse($this->Component->startup($this->Controller));

		$this->Component->allow('camelCase');
		$this->Component->deny();

		$this->Controller->request['action'] = 'camelCase';
		$this->assertFalse($this->Component->startup($this->Controller));

		$this->Controller->request['action'] = 'login';
		$this->assertFalse($this->Component->startup($this->Controller));

		$this->Component->deny();
		$this->Component->allow(null);

		$this->Controller->request['action'] = 'camelCase';
		$this->assertTrue($this->Component->startup($this->Controller));

		$this->Component->allow();
		$this->Component->deny(null);

		$this->Controller->request['action'] = 'camelCase';
		$this->assertFalse($this->Component->startup($this->Controller));
	}

	/**
	 * test that deny() converts camel case inputs to lowercase.
	 *
	 * @return void
	 */
	public function testDenyWithCamelCaseMethods() {
		// Set up fake auth objects to isolate tests to StatelessAuthComponent only.
		$this->initComponentAuthObjects();

		$this->Component->allow();
		$this->Component->deny('add', 'camelCase');

		$url = '/auth_test/camelCase';
		$this->Controller->request->addParams(Router::parse($url));
		$this->Controller->request->query['url'] = Router::normalize($url);

		$this->assertFalse($this->Component->startup($this->Controller));

		$url = '/auth_test/CamelCase';
		$this->Controller->request->addParams(Router::parse($url));
		$this->Controller->request->query['url'] = Router::normalize($url);
		$this->assertFalse($this->Component->startup($this->Controller));
	}

	/**
	 * test that allow() and allowedActions work with camelCase method names.
	 *
	 * @return void
	 */
	public function testAllowedActionsWithCamelCaseMethods() {
		$url = '/auth_test/camelCase';
		$this->Controller->request->addParams(Router::parse($url));
		$this->Controller->request->query['url'] = Router::normalize($url);
		$this->initComponentAuthObjects();
		$this->Component->loginAction = array('controller' => 'AuthTest', 'action' => 'login');
		$this->Component->userModel = 'StatelessAuthUser';
		$this->Component->allow();
		$result = $this->Component->startup($this->Controller);
		$this->assertTrue($result, 'startup() should return true, as action is allowed. %s');

		$url = '/auth_test/camelCase';
		$this->Controller->request->addParams(Router::parse($url));
		$this->Controller->request->query['url'] = Router::normalize($url);
		$this->initComponentAuthObjects();
		$this->Component->loginAction = array('controller' => 'AuthTest', 'action' => 'login');
		$this->Component->userModel = 'StatelessAuthUser';
		$this->Component->allowedActions = array('delete', 'camelCase', 'add');
		$result = $this->Component->startup($this->Controller);
		$this->assertTrue($result, 'startup() should return true, as action is allowed. %s');

		$this->Component->allowedActions = array('delete', 'add');
		$result = $this->Component->startup($this->Controller);
		$this->assertFalse($result, 'startup() should return false, as action is not allowed. %s');

		$url = '/auth_test/delete';
		$this->Controller->request->addParams(Router::parse($url));
		$this->Controller->request->query['url'] = Router::normalize($url);
		$this->initComponentAuthObjects();
		$this->Component->loginAction = array('controller' => 'AuthTest', 'action' => 'login');
		$this->Component->userModel = 'StatelessAuthUser';

		$this->Component->allow(array('delete', 'add'));
		$result = $this->Component->startup($this->Controller);
		$this->assertTrue($result, 'startup() should return true, as action is allowed. %s');
	}

	/**
	 * Test allowed actions (via startup().)
	 *
	 * @return void
	 */
	public function testAllowedActionsSetWithAllowMethod() {
		$url = '/auth_test/action_name';
		$this->Controller->request->addParams(Router::parse($url));
		$this->Controller->request->query['url'] = Router::normalize($url);
		$this->Component->initialize($this->Controller);
		$this->Component->allow('action_name', 'anotherAction');
		$this->assertEquals(array('action_name', 'anotherAction'), $this->Component->allowedActions);
	}

	/**
	 * Test isAuthorized().
	 *
	 * @return void
	 */
	public function testIsAuthorized() {
		$this->initComponentAuthObjects(false, false);
		$this->Component->setUser(null);
		$this->assertFalse(
			$this->Component->isAuthorized(null),
			'Should return false when no user stored and no user provided.'
		);

		$this->initComponentAuthObjects(true, true);
		$user = array('canary');
		$this->Component->setUser($user);

		$this->assertTrue(
			$this->Component->isAuthorized(),
			'Should return true when delegated authorizeObject->authorize() succeeds.'
		);
	}

	/**
	 * Test constructAuthObject(), via constructAuthenticate(), when no object is named.
	 *
	 * @covers StatelessAuthComponent::constructAuthObject
	 * @return void
	 */
	public function testConstructAuthObjectNoAuthenticateProperty() {
		$this->Component->authenticate = null;
		$this->assertEquals(
			null,
			$this->Component->constructAuthenticate(),
			'Should return null when no ::authenticate record is available.'
		);
	}

	/**
	 * Test constructAuthObject(), via constructAuthenticate(), when a
	 * nonexistent class name is provided.
	 *
	 * @covers StatelessAuthComponent::constructAuthObject
	 * @return void
	 */
	public function testConstructAuthObjectBadClassName() {
		// Test with a class name that does not exist.
		$this->expectException('CakeException', 'Authentication adapter "DoesNotExist" was not found.');
		$this->Component->authenticate = array(
			'className' => 'DoesNotExist',
		);
		$this->Component->constructAuthenticate();
	}

	/**
	 * Test constructAuthObject(), via constructAuthenticate(), when a
	 * the named object is missing the required method.
	 *
	 * @covers StatelessAuthComponent::constructAuthObject
	 * @return void
	 */
	public function testConstructAuthObjectMissingRequiredMethod() {
		// Test with an object that is missing the required method.
		$this->expectException('CakeException', 'Authentication object must implement an authenticate() method.');
		$this->Component->authenticate = array(
			'className' => 'HasNoAuthenticateMethod',
		);
		$this->Component->constructAuthenticate();
	}
}
