<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case.Controller.Component.Auth
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('TokenLoginLogoutAuthenticate', 'StatelessAuth.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');

// test classes for mocking
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DS . "test_classes.php";

/**
 * Test case for FormAuthentication
 *
 * @package       Cake.Test.Case.Controller.Component.Auth
 */
class TokenLoginLogoutAuthenticateTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
	);

	/**
	 * setup
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->Collection = $this->getMock('ComponentCollection');
		$this->auth = $this->getMock('TestTokenLoginLogoutAuthenticate',
			null,
			array(
				$this->Collection,
				array(
					'userModel' => 'StatelessAuthUserWithMethods',
					'fields' => array(
						'username' => 'username',
						'password' => 'password',
						'token' => 'token',
					),
				),
			)
		);

		$password = Security::hash('password', null, true);
		$User = ClassRegistry::init('StatelessAuthUserWithMethods');
		$User->updateAll(array('password' => $User->getDataSource()->value($password)));
		$this->response = $this->getMock('CakeResponse');
	}

	/**
	 * Test applying settings in the constructor.
	 *
	 * @return void
	 */
	public function testConstructorSuccess() {
		$settings = array(
			'userModel' => 'StatelessAuthUserWithMethods',
			'fields' => array(
				'username' => 'email',
				'password' => 'pass',
				'token' => 'oauth',
			),
		);
		$Controller = new Controller();
		$this->Collection = new ComponentCollection($Controller);
		$object = new TokenLoginLogoutAuthenticate($this->Collection, $settings);

		$this->assertEquals($settings['userModel'], $object->settings['userModel']);
		$this->assertEquals($settings['fields'], $object->settings['fields']);
	}

	/**
	 * Test applying settings in the constructor.
	 *
	 * @return void
	 */
	public function testConstructorFailsWhenUserMethodsMissing() {
		$settings = array(
			'userModel' => 'StatelessAuthUser',
			'fields' => array(
				'username' => 'email',
				'password' => 'pass',
				'token' => 'oauth',
			),
		);
		$Controller = new Controller();
		$this->Collection = new ComponentCollection($Controller);

		$this->expectException(
			'StatelessAuthMissingMethodException',
			'TokenLoginLogoutAuthenticate requires the StatelessAuthUser model to define a `public function login($username, $password) => array|false` method.'
		);

		$object = new TokenLoginLogoutAuthenticate($this->Collection, $settings);
	}

	/**
	 * Test authenticate success.
	 *
	 * @return void
	 */
	public function testAuthenticateSuccess() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array( // !! No wrapping [User] array should still be accepted !!
			'username' => 'test',
			'password' => 'test',
		);

		// Execute the SUT and check the direct returned result.
		$result = $this->auth->authenticate($request, $this->response);
		$this->assertEquals(
			'login',
			$result,
			'authenticate() must pass back the User records provided by the User model.'
		);
	}

	/**
	 * Test checkFields() failure in authenticate() whe no POST data supplied.
	 *
	 * @return void
	 */
	public function testAuthenticateEmptyDataFails() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array();

		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

	/**
	 * Test checkFields() failure in authenticate() when missing username/password POST data.
	 *
	 * @return void
	 */
	public function testAuthenticateCheckFieldsFails() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array('StatelessAuthUserWithMethods' => array(
			'username' => 'test',
			'password' => null, // Should count as "empty" and fail checkFields().
		));

		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

	/**
	 * Test _findUser() failure in authenticate().
	 *
	 * @return void
	 */
	public function testAuthenticateFindUserFails() {
		$request = new CakeRequest('posts/index', false);
		$request->data = array('User' => array(
			'username' => 'does-not-exist',
			'password' => 'test',
		));
		$this->auth->UserModel = $this->getMock('StatelessAuthUserWithMethods', array('login'));
		$this->auth->UserModel->expects($this->once())
			->method('login')
			->with($request->data['User']['username'], $request->data['User']['password'])
			->will($this->returnValue(false));

		$this->assertFalse($this->auth->authenticate($request, $this->response));
	}

	/**
	 * Test that the logout method clears the token for the provided User.
	 *
	 * @return void
	 */
	public function testLogout() {
		$user = array(
			'id' => 'abcdef',
			'token' => null,
		);
		$result = $this->auth->logout($user);
		$this->assertEquals(
			'logout',
			$result,
			'Logout on a Model that does have logout method, mocked to return canary.'
		);
	}

	/**
	 * Test the ability to check for a header and fetch the correct User record.
	 *
	 * @return void
	 */
	public function testGetUserSucceeds() {
		$token = 'foobar';
		$user = array(
			'User' => array(
				'id' => 'abcdef',
			),
		);
		$request = new CakeRequest();

		// Set up a fake User model to return a dummy record.
		$this->auth->UserModel = $this->getMockForModel('StatelessAuthUserWithMethods', array('findForToken'));
		$this->auth->UserModel->expects($this->once())
			->method('findForToken')
			->with($token)
			->will($this->returnValue($user));

		// Replace our accessor methods to return the dummy User model instance and dummy token.
		$this->auth = $this->getMock('TokenLoginLogoutAuthenticate',
			array('getToken'),
			array(new ComponentCollection(), array())
		);
		$this->auth->expects($this->once())
			->method('getToken')
			->with($request)
			->will($this->returnValue($token));

		// Execute the SUT and check the direct returned result.
		$result = $this->auth->getUser($request);
		$this->assertEquals(
			$user['User'],
			$result,
			'getUser() should return the expected User array.'
		);
	}

	/**
	 * Ensure getUser fails on a bad token.
	 *
	 * @return void
	 */
	public function testGetUserFails() {
		$token = 'foobar';
		$user = array(
			'User' => array(
				'id' => 'abcdef',
			),
		);
		$request = new CakeRequest();

		// Set up a fake User model to return a dummy record.
		$this->auth->UserModel = $this->getMockForModel('StatelessAuthUserWithMethods', array('findForToken'));
		$this->auth->UserModel->expects($this->once())
			->method('findForToken')
			->with($token)
			->will($this->returnValue(false));

		// Replace our accessor methods to return the dummy User model instance and dummy token.
		$this->auth = $this->getMock('TokenLoginLogoutAuthenticate',
			array('getToken'),
			array(new ComponentCollection(), array())
		);
		$this->auth->expects($this->once())
			->method('getToken')
			->with($request)
			->will($this->returnValue($token));

		// Execute the SUT and check the direct returned result.
		$this->expectException(
			'StatelessAuthUnauthorizedException',
			'Missing, invalid or expired token present in request. Include an HTTP_AUTHORIZATION header, or please login to obtain a token.'
		);
		$result = $this->auth->getUser($request);
	}

	/**
	 * Test that getModel fetches the expected object from the ClassRegistry.
	 *
	 * @return void
	 */
	public function testGetModel() {
		$this->auth->settings['userModel'] = 'TestCanary';
		$result = $this->auth->getModel();
		$this->assertEquals(
			'fizzbuzz',
			$result->property,
			'getModel() should return the `User` object from the classRegistry.'
		);
	}

	/**
	 * Test that getToken asks for the expected $_SERVER value and
	 * processes the result cleanly.
	 *
	 * @return void
	 */
	public function testGetToken() {
		$token = 'canary';
		$request = $this->getMock('CakeRequest', array('header'));
		$request->staticExpects($this->once())
			->method('header')
			->with('Authorization')
			->will($this->returnValue('Bearer ' . $token));

		$result = $this->auth->getToken($request);
		$this->assertEquals(
			'canary',
			$result,
			'getToken() should read the correct `$_SERVER` value and strip any leading `Bearer`.'
		);
	}
}
