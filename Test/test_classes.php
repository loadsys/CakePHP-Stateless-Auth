<?php
/**
 * TestClasses used for mocking and injection of dummy classes for Testing the StatelessAuth Plugin
 */
App::uses('StatelessAuthComponent', 'StatelessAuth.Controller/Component');
App::uses('TokenLoginLogoutAuthenticate', 'StatelessAuth.Controller/Component/Auth');
App::uses('AppModel', 'Model');
App::uses('Controller', 'Controller');
App::import('Lib/Error', 'StatelessAuth.StatelessAuthExceptions');

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
 * A test controller that defines the mandatory ::$privilege property.
 *
 * Also uses the test model that defines necessary methods for
 * TokenLoginLogoutAuthenticate.
 *
 */
class HasPrivilegePropertyLoginLogoutController extends HasPrivilegePropertyController {

	/**
	 * uses property
	 *
	 * @var array
	 */
	public $uses = array('StatelessAuthUserWithMethods');
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

	/**
	 * name property
	 *
	 * @var string
	 */
	public $name = 'User';

}

/**
 * StatelessAuthUserWithMethods class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class StatelessAuthUserWithMethods extends CakeTestModel {

	/**
	 * useDbConfig property
	 *
	 * @var string
	 */
	public $useDbConfig = 'test';

	/**
	 * name property
	 *
	 * @var string
	 */
	public $name = 'User';

	/**
	 * login method
	 *
	 * @return void
	 */
	public function login($user, $pass) {
		return 'login';
	}

	/**
	 * logout method
	 *
	 * @return void
	 */
	public function logout($user) {
		return 'logout';
	}

	/**
	 * updateLastLogin method
	 *
	 * @return void
	 */
	public function updateLastLogin($userId) {
		return 'updateLastLogin';
	}

	/**
	 * findForToken method
	 *
	 * @return void
	 */
	public function findForToken($token) {
		return 'findForToken';
	}
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

/**
 * Expose protected properties in TokenLoginLogoutAuthenticate for testing.
 */
class TestTokenLoginLogoutAuthenticate extends TokenLoginLogoutAuthenticate {
	public $_Collection = null;
	public $UserModel = null;
}
