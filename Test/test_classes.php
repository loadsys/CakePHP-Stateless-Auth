<?php
/**
 * TestClasses used for mocking and injection of dummy classes for Testing the StatelessAuth Plugin
 */
App::uses('StatelessAuthComponent', 'StatelessAuth.Controller/Component');
App::uses('AppModel', 'Model');

/**
 * Exposes protected properties via setters.
 *
 */
class TestStatelessAuthComponent extends StatelessAuthComponent {
	public $authenticateObject;
	public $authorizeObject;
	public function setUser($user) {
		$this->user = $user;
	}
	public function whichUser($user = null) {
		return parent::whichUser($user);
	}
}

/**
 * A test controller that does not define the mandatory ::$privilege property.
 *
 */
class MissingHasPrivilegePropertyController extends Controller {
}

/**
 * A test controller that defines the mandatory ::$privilege property.
 *
 */
class HasPrivilegePropertyController extends Controller {

	/**
	 * privilege property
	 *
	 * @var array
	 */
	public $privilege = 'users';

	/**
	 * components property
	 *
	 * @var array
	 */
	public $components = array('Auth');

	/**
	 * uses property
	 *
	 * @var array
	 */
	public $uses = array('StatelessAuthUser');

	/**
	 * testUrl property
	 *
	 * @var mixed
	 */
	public $testUrl = null;

	/**
	 * construct method
	 *
	 */
	public function __construct($request, $response) {
		$request->addParams(Router::parse('/auth_test'));
		$request->here = '/auth_test';
		$request->webroot = '/';
		Router::setRequestInfo($request);
		parent::__construct($request, $response);
	}

	/**
	 * login method
	 *
	 * @return void
	 */
	public function login() {
	}

	/**
	 * admin_login method
	 *
	 * @return void
	 */
	public function admin_login() {
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add() {
	}

	/**
	 * logout method
	 *
	 * @return void
	 */
	public function logout() {
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		echo "add";
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function camelCase() {
		echo "camelCase";
	}
}

/**
 * StatelessAuthUser class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class StatelessAuthUser extends CakeTestModel {

	/**
	 * useDbConfig property
	 *
	 * @var string
	 */
	public $useDbConfig = 'test';
	public $name = 'User';

}

/**
 * An Authentication object that does not define the required
 * ::authenticate() method.
 *
 * Used for testing StatelessAuthComponent::constructAuthenticate() and
 * ::constructAuthorize().
 */
class HasNoAuthenticateMethodAuthenticate {
}

/**
 * Dummy model to test that objects are returned from getModel() correctly.
 */
class TestCanary extends AppModel {
	public $property = 'fizzbuzz';
}
