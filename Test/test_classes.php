<?php
/**
 * TestClasses used for mocking and injection of dummy classes for Testing the StatelessAuth Plugin
 */
App::uses('StatelessAuthComponent', 'StatelessAuth.Controller/Component');
App::uses('TokenLoginLogoutAuthenticate', 'StatelessAuth.Controller/Component/Auth');
App::uses('AppModel', 'Model');
App::uses('Controller', 'Controller');
// Load CakePHP Stateless Auth Exceptions
App::import('Lib/Error', 'StatelessAuth.StatelessAuthException');

/**
 * Exposes protected properties via setters.
 *
 */
class TestStatelessAuthComponent extends StatelessAuthComponent {

	/**
	 * Expose the authenticateObject to allow mock injection.
	 *
	 * @var object
	 */
	public $authenticateObject;

	/**
	 * Expose the authorizeObject to allow mock injection.
	 *
	 * @var object
	 */
	public $authorizeObject;

	/**
	 * Expose a setter to allow injection of current user.
	 *
	 * @param array $user User data to inject into the component.
	 * @return void
	 */
	public function setUser($user) {
		$this->user = $user;
	}

	/**
	 * Expose protected method for direct testing.
	 *
	 * @param array $user User data to inject into the component.
	 * @return mixed
	 */
	public function whichUser($user = null) {
		return parent::whichUser($user);
	}

}

/**
 * A test controller that defines the mandatory ::$privilege property.
 *
 */
class StatelessAuthController extends Controller {

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
	 * @param CakeRequest $request Current request.
	 * @param CakeResponse $response Current request.
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
	 * @param string $user Username.
	 * @param string $pass Password.
	 * @return void
	 */
	public function login($user, $pass) {
		return 'login';
	}

	/**
	 * logout method
	 *
	 * @param array $user User record.
	 * @return void
	 */
	public function logout($user) {
		return 'logout';
	}

	/**
	 * updateLastLogin method
	 *
	 * @param string $userId User.id.
	 * @return void
	 */
	public function updateLastLogin($userId) {
		return 'updateLastLogin';
	}

	/**
	 * findForToken method
	 *
	 * @param string $token HTTP Authorization token.
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

	/**
	 * property property
	 *
	 * @var string
	 */
	public $property = 'fizzbuzz';
}

/**
 * Expose protected properties in TokenLoginLogoutAuthenticate for testing.
 */
class TestTokenLoginLogoutAuthenticate extends TokenLoginLogoutAuthenticate {

	/**
	 * _Collection property
	 *
	 * @var mixed
	 */
	public $_Collection = null;

	/**
	 * UserModel property
	 *
	 * @var Model
	 */
	public $UserModel = null;
}
