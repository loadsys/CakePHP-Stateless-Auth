<?php
/**
 * Wraps tests of the IsAuthorizedAuthorize object in a crude
 * ControllerTestCase for easy access.
 */

App::uses('Controller', 'Controller');
App::uses('IsAuthorizedAuthorize', 'StatelessAuth.Controller/Component/Auth');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');

/**
 * Class IsAuthorizedAuthorizeControllerTest
 *
 * @package       Cake.Test.Case.Controller.Component.Auth
 */
class IsAuthorizedAuthorizeControllerTest extends CakeTestCase {

	/**
	 * setup
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->initSUT('Controller', array('isAuthorized'));
	}

	/**
	 * Encapsulates authorization object setup.
	 * Allows methods on the attached controller and the authorize object
	 * itself to be overriden by passing arrays of method names.
	 *
	 * @param string $controller A controller class name to use for the controller mock.
	 * @param array $controllerMocks A list of controller methods to mock.
	 * @param array|false $authMocks If an array is provided, IsAuthorizedAuthorize is mocked with the list of methods, otherwise a real authorize object is instantiated.
	 * @return void
	 */
	public function initSUT($controller = 'Controller', $controllerMocks = array(), $authMocks = false) {
		$this->controller = $this->getMock($controller,
			$controllerMocks,
			array(),
			'',
			false
		);
		$this->components = $this->getMock('ComponentCollection');
		$this->components->expects($this->any())
			->method('getController')
			->will($this->returnValue($this->controller));

		if (is_array($authMocks)) {
			$this->auth = $this->getMock('IsAuthorizedAuthorize', $authMocks, array($this->components));
		} else {
			$this->auth = new IsAuthorizedAuthorize($this->components);
		}
	}

	/**
	 * When a controller does not define an ::isAuthorized() method, throw
	 * an Exception.
	 *
	 * @return void
	 */
	public function testAuthorizeControllerIsAuthorizedMethodMissing() {
		$user = array('username' => 'mark');
		$request = new CakeRequest('/posts/index', false);

		$this->initSUT('Controller'); // Don't mock isAuthorized().
		$this->expectException('NotImplementedException');
		$this->auth->authorize($user, $request);
	}

	/**
	 * Check that we pass through the controller's isAuthorized() responses.
	 *
	 * @dataProvider provideAuthorizeArgs
	 * @return void
	 */
	public function testAuthorize($isAuthorizedReturnValue, $expected, $msg = '') {
		$user = array('username' => 'mark');
		$request = new CakeRequest('/posts/index', false);

		$this->controller->expects($this->once())
			->method('isAuthorized')
			->with($user)
			->will($this->returnValue($isAuthorizedReturnValue));

		$this->assertEquals(
			$expected,
			$this->auth->authorize($user, $request),
			$msg
		);
	}

	/**
	 * Returns sets of [isAuthorizedReturnValue, expected, msg] to the
	 * authorize() tests.
	 *
	 * @return array
	 */
	public function provideAuthorizeArgs() {
		return array(
			array(true, true, 'When the controller defines an isAuthorized method and it returns true, authorization should succeed.'),
			array(false, false, 'When the controller defines an isAuthorized method and it returns false, authorization should fail.'),
		);
	}
}
