<?php
/**
 * ControllerAuthorizeTest file
 *
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

App::uses('Controller', 'Controller');
App::uses('PrivilegeAuthorize', 'StatelessAuth.Controller/Component/Auth');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');


/**
 * Does not define the mandatory ::$privilege property, forcing an exception
 * to be thrown.
 */
class NoPrivilegePropertyController extends Controller {
}

/**
 * Define the mandatory ::$privilege property.
 */
class SamplePrivilegePropertyController extends Controller {
	public $privilege = 'sample';
}

/**
 * Define the mandatory ::$privilege property and optional
 * readActions() / writeActions().
 */
class SampleReadWriteActionsController extends Controller {
	public $privilege = 'foo';
	public function readActions() {
		return array('readable');
	}
	public function writeActions() {
		return array('readable', 'writeable');
	}
}

/**
 * Class ControllerAuthorizeTest
 *
 * @package       Cake.Test.Case.Controller.Component.Auth
 */
class ControllerAuthorizeTest extends CakeTestCase {

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
	 * Encapsulates authorization object setup. Allows methods on the attached controller and the authorize object itself to be overriden by passing arrays of method names.
	 *
	 * @param string $controller A controller class name to use for the controller mock.
	 * @param array $controllerMocks A list of controller methods to mock.
	 * @param array|false $authMocks If an array is provided, PrivilegeAuthorize is mocked with the list of methods, otherwise a real object is instatiated.
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
			$this->auth = $this->getMock('PrivilegeAuthorize', $authMocks, array($this->components));
		} else {
			$this->auth = new PrivilegeAuthorize($this->components);
		}
	}

	/**
	 * Test isAuthorized checking/return working.
	 *
	 * @return void
	 */
	public function testAuthorizeIsAuthorizedSucceeds() {
		$user = array('username' => 'mark');
		$request = new CakeRequest('/posts/index', false);

		$this->controller->expects($this->once())
			->method('isAuthorized')
			->with($user)
			->will($this->returnValue(true));

		$this->assertTrue(
			$this->auth->authorize($user, $request),
			'When the controller defines an isAuthorized method and it returns true, authorization should succeed.'
		);
	}

	/**
	 * Test isAuthorized checking/return working.
	 *
	 * @return void
	 */
	public function testAuthorizeIsAuthorizedFails() {
		$user = array('username' => 'mark');
		$request = new CakeRequest('/posts/index', false);

		$this->controller->expects($this->once())
			->method('isAuthorized')
			->with($user)
			->will($this->returnValue(false));

		$this->expectException('ForbiddenByPermissionsException');
		$this->auth->authorize($user, $request);
	}

	/**
	 * Test Controller::$privilege check works.
	 *
	 * @return void
	 */
	public function testAuthorizeWithNoControllerPrivilegePropertyFails() {
		$this->initSUT('NoPrivilegePropertyController');
		$user = array('username' => 'mark');
		$request = new CakeRequest('/posts/index', false);

		$this->expectException('NotImplementedException', 'Controller does not define the mandatory `::$privilege` property used for authorization. See AppController::$privilege for further explanation.');
		$this->auth->authorize($user, $request);
	}

	/**
	 * Test check for $user['Permissions'] key works.
	 *
	 * @return void
	 */
	public function testAuthorizeWithMissingUserPermisionsKeyFails() {
		$this->initSUT('SamplePrivilegePropertyController');
		$user = array(
			'username' => 'mark',
			// [Permission] key intentionally missing.
		);
		$request = new CakeRequest('/posts/index', false);

		$this->expectException('ForbiddenByPermissionsException');
		$this->auth->authorize($user, $request);
	}


	/**
	 * Test false userHasAccess response when debug is on.
	 *
	 * @return void
	 */
	public function testAuthorizeWithNoUserAccessAndDebugOnFails() {
		$this->initSUT('SamplePrivilegePropertyController', array(), array('userHasAccess'));
		$user = array(
			'username' => 'mark',
			'Permission' => array(
				'sample' => 'none',
			),
		);
		$request = new CakeRequest('/sample_privilege_properties/index', false);
		$request->params['action'] = 'index';

		$this->auth->expects($this->once())
			->method('userHasAccess')
			->with($user['Permission'], 'sample', 'index')
			->will($this->returnValue(false));

		Configure::write('debug', 2);

		$this->expectException('ForbiddenByPermissionsException');
		$this->auth->authorize($user, $request);
	}

	/**
	 * Test false userHasAccess response when debug is off.
	 *
	 * @return void
	 */
	public function testAuthorizeWithNoUserAccessAndDebugOffFails() {
		$this->initSUT('SamplePrivilegePropertyController', array(), array('userHasAccess'));
		$user = array(
			'username' => 'mark',
			'Permission' => array(
				'sample' => 'none',
			),
		);
		$request = new CakeRequest('/sample_privilege_properties/index', false);
		$request->params['action'] = 'index';

		$this->auth->expects($this->once())
			->method('userHasAccess')
			->with($user['Permission'], 'sample', 'index')
			->will($this->returnValue(false));

		Configure::write('debug', 0);

		$this->expectException('NotFoundException');
		$this->auth->authorize($user, $request);
	}

	/**
	 * Test true userHasAccess response.
	 *
	 * @return void
	 */
	public function testAuthorizeSucceeds() {
		$this->initSUT('SamplePrivilegePropertyController', array(), array('userHasAccess'));
		$user = array('User' => array( // Extra wrapping [User] key on this one too.
			'username' => 'mark',
			'Permission' => array(
				'sample' => 'write',
			),
		));
		$request = new CakeRequest('/sample_privilege_properties/index', false);
		$request->params['action'] = 'index';

		$this->auth->expects($this->once())
			->method('userHasAccess')
			->with($user['User']['Permission'], 'sample', 'index')
			->will($this->returnValue(true));

		$this->assertTrue(
			$this->auth->authorize($user, $request),
			'authorize() should succeed.'
		);
	}

	/**
	 * Test userHasAccess basic logic.
	 *
	 * @dataProvider provideUserHasAccessArgs
	 * @return void
	 */
	public function testUserHasAccess($userRead, $isRead, $userWrite, $isWrite, $expected, $msg = '') {
		$priv = 'sample';
		$action = 'index';
		$dummyPerms = array();

		$this->initSUT('SamplePrivilegePropertyController', array(), array(
			'userCanRead', 'userCanWrite', 'isReadAction', 'isWriteAction',
		));
		$this->auth->expects($this->any())
			->method('userCanRead')
			->with($dummyPerms, $priv)
			->will($this->returnValue($userRead));
		$this->auth->expects($this->any())
			->method('isReadAction')
			->with($action)
			->will($this->returnValue($isRead));

		$this->auth->expects($this->any())
			->method('userCanWrite')
			->with($dummyPerms, $priv)
			->will($this->returnValue($userWrite));
		$this->auth->expects($this->any())
			->method('isWriteAction')
			->with($action)
			->will($this->returnValue($isWrite));

		$this->assertEquals(
			$expected,
			$this->auth->userHasAccess($dummyPerms, $priv, $action),
			$msg
		);
	}

	/**
	 * Provides args for testing userHasAccess().
	 *
	 * @return array
	 */
	public function provideUserHasAccessArgs() {
		return array(
			array(
				true, true, null, null, // userCanRead, isReadAction, userCanWrite, isWriteAction
				true, 'User can read and action is read, should succeed.', // expected, msg
			),

			array(
				true, false, true, true,
				true, 'User can write and action is write, should succeed.',
			),

			array(
				true, false, false, true,
				false, 'User can only read and action is write, should fail.',
			),
		);
	}

	/**
	 * Test userCanRead() basic logic.
	 *
	 * @dataProvider provideUserCanReadUserCanWriteArgs
	 * @return void
	 */
	public function testUserCanRead($access, $expectedReadResult, $expectedWriteResult, $msg = '') {
		$priv = 'sample';
		$dummyPerms = array();

		$this->initSUT('SamplePrivilegePropertyController', array(), array(
			'userAccessForPrivilege',
		));
		$this->auth->expects($this->any())
			->method('userAccessForPrivilege')
			->with($dummyPerms, $priv)
			->will($this->returnValue($access));

		$this->assertEquals(
			$expectedReadResult,
			$this->auth->userCanRead($dummyPerms, $priv),
			$msg
		);
	}

	/**
	 * Test userCanWrite() basic logic.
	 *
	 * @dataProvider provideUserCanReadUserCanWriteArgs
	 * @return void
	 */
	public function testUserCanWrite($access, $expectedReadResult, $expectedWriteResult, $msg = '') {
		$priv = 'sample';
		$dummyPerms = array();

		$this->initSUT('SamplePrivilegePropertyController', array(), array(
			'userAccessForPrivilege',
		));
		$this->auth->expects($this->any())
			->method('userAccessForPrivilege')
			->with($dummyPerms, $priv)
			->will($this->returnValue($access));

		$this->assertEquals(
			$expectedWriteResult,
			$this->auth->userCanWrite($dummyPerms, $priv),
			$msg
		);
	}

	/**
	 * Provides args for testing userCanRead() and userCanWrite() tests.
	 *
	 * @return array
	 */
	public function provideUserCanReadUserCanWriteArgs() {
		return array(
			array(
				'unrecognized', false, false,
				'User has `unrecognized` access: read should fail, write should fail.',
			),

			array(
				'none', false, false, // access response, expected userCanRead return, expected userCanWrite return
				'User has `none` access: read should fail, write should fail.', // msg
			),

			array(
				'read', true, false,
				'User has `read` access: read should succeed, write should fail.',
			),

			array(
				'write', true, true,
				'User has `write` access: read should succeed, write should succeed.',
			),
		);
	}

	/**
	 * Test isReadAction() basic logic.
	 *
	 * @return void
	 */
	public function testIsReadAction() {
		$this->assertFalse(
			$this->auth->isReadAction('readable'),
			'Controller uses default readable actions, of which `readable` is not among them.'
		);
		$this->assertTrue(
			$this->auth->isReadAction('index'),
			'Controller uses default readable actions, of which `index` is among them.'
		);
		$this->assertFalse(
			$this->auth->isReadAction('edit'),
			'Controller uses default readable actions, of which `edit` is not among them.'
		);

		// Test again with our own defined actions.
		$this->initSUT('SampleReadWriteActionsController', null, false);
		$this->assertTrue(
			$this->auth->isReadAction('readable'),
			'Controller provides its own list of readable actions, of which `readable` is among them.'
		);
		$this->assertFalse(
			$this->auth->isReadAction('index'),
			'Controller provides its own list of readable actions, of which `index` should not be among them.'
		);
		$this->assertFalse(
			$this->auth->isReadAction('writeable'),
			'Controller provides its own list of readable actions, of which the known writeable `writeable` action should not be among them.'
		);
	}

	/**
	 * Test isWriteAction() basic logic.
	 *
	 * @return void
	 */
	public function testIsWriteAction() {
		$this->assertFalse(
			$this->auth->isWriteAction('readable'),
			'Controller uses default readable actions, of which `readable` is not among them.'
		);
		$this->assertTrue(
			$this->auth->isWriteAction('index'),
			'Controller uses default readable actions, of which `index` is among them.'
		);
		$this->assertTrue(
			$this->auth->isWriteAction('edit'),
			'Controller uses default readable actions, of which `edit` is among them.'
		);

		// Test again with our own defined actions.
		$this->initSUT('SampleReadWriteActionsController', null, false);
		$this->assertTrue(
			$this->auth->isWriteAction('readable'),
			'Controller provides its own list of readable actions, of which `readable` is among them.'
		);
		$this->assertFalse(
			$this->auth->isWriteAction('index'),
			'Controller provides its own list of readable actions, of which `index` should not be among them.'
		);
		$this->assertTrue(
			$this->auth->isWriteAction('writeable'),
			'Controller provides its own list of readable actions, of which the known writeable `writeable` action should not be among them.'
		);
	}

	/**
	 * Test userAccessForPrivilege() basic logic. Also provides coverage
	 * for greaterPrivilege().
	 *
	 * @dataProvider provideUserAccessForPrivilegeArgs
	 * @return void
	 */
	public function testUserAccessForPrivilege($userPerms, $expected, $msg = '') {
		$priv = 'sample';

		$this->assertEquals(
			$expected,
			$this->auth->userAccessForPrivilege($userPerms, $priv),
			$msg
		);
	}

	/**
	 * Provides args for testing userAccessForPrivilege() tests.
	 *
	 * @return array
	 */
	public function provideUserAccessForPrivilegeArgs() {
		return array(
			array(
				'not-an-array', // User's [Permission] array.
				false, // expected
				'When User does not have a defined [Permission] array, return false.' // msg
			),

			array(
				array(
					'sample-key-not-present' => 'read',
				),
				false,
				'When User does not have a defined [Permission][sample] key, return false.'
			),

			array(
				array(
					'sample' => 'read',
					'admin' => 'none',
				),
				'read',
				'When User does has better section access than admin access, use it.'
			),

			array(
				array(
					'sample' => 'write',
					'admin' => 'read',
				),
				'write',
				'When User does has better section access than admin access, use it.'
			),

			array(
				array(
					'sample' => 'none',
					'admin' => 'write',
				),
				'write',
				'When User does has better admin access than section access, use it.'
			),

			array(
				array(
					'sample' => 'none',
					'admin' => 'none',
				),
				'none',
				'If User has no access to admin or section, return `none`.'
			),

			array(
				array(
					'sample' => 'bad-value',
					'admin' => 'not-recognized',
				),
				'none',
				'If we somehow end up with valid keys but bogus values, always return `none` access.'
			),

			array(
				array(
					'sample' => 'read',
				),
				'read',
				'User has `read` access to `sample`.',
			),
		);
	}
}
