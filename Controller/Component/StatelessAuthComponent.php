<?php
/**
 * Authorization component
 *
 * Provides access to convenience methods for checking a User's access to
 * sections of the app. Uses the basic IsAuthorized authorize object by
 * default.
 *
 */

App::uses('Hash', 'Utility');
App::uses('Component', 'Controller');

/**
 * Authentication and authorization control component class.
 *
 * Provides stateless authentication via `Bearer` header tokens and
 * authorization using the basic IsAuthorized authorize object. (Both by
 * default.)
 *
 * @package StatelessAuth.Controller.Component
 */
class StatelessAuthComponent extends Component {

	/**
	 * Request object
	 *
	 * @var CakeRequest
	 */
	public $request;

	/**
	 * Response object
	 *
	 * @var CakeResponse
	 */
	public $response;

	/**
	 * A URL (defined as a string or array) to the controller action that handles
	 * logins. Defaults to `/users/login`.
	 *
	 * @var mixed
	 */
	public $loginAction = array(
		'controller' => 'users',
		'action' => 'login',
		'plugin' => null
	);

	/**
	 * Controller actions for which user validation is not required.
	 *
	 * @var array
	 * @see StatelessAuthComponent::allow()
	 */
	public $allowedActions = array();

	/**
	 * An array of settings for the authentication object to use for authenticating users.
	 *
	 * {{{
	 *	$this->Auth->authenticate = array(
	 *		'className' => 'StatelessAuth.Token'
	 *		'userModel' => 'User'
	 *	);
	 * }}}
	 *
	 * The `className` key is required.
	 *
	 * @var array
	 * @see StatelessAuthComponent::constructAuthenticate()
	 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html
	 */
	public $authenticate = array(
		'className' => 'StatelessAuth.Token',
		'userModel' => 'User',
	);

	/**
	 * An array of settings for the authorization objects to use for authorizing users.
	 *
	 * {{{
	 *	$this->Auth->authorize = array(
	 *		'className' => 'StatelessAuth.IsAuthorized'
	 *	);
	 * }}}
	 *
	 * The `className` key is required.
	 *
	 * @var array
	 * @see StatelessAuthComponent::constructAuthorize()
	 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html
	 */
	public $authorize = array(
		'className' => 'StatelessAuth.IsAuthorized',
		'userModel' => 'User',
	);

	/**
	 * The Privilege.slug to use for all queries when one is not explicitly
	 * provided. Initialized to the attached $controller's ::$privilege
	 * property during startup().
	 *
	 * @var string
	 */
	protected $privilege;

	/**
	 * The name of the "current" controller action. Initialized to
	 * $request->params['action'] during startup().
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * The logged-in User represented by the provided HTTP `Authorization` header token value.
	 *
	 * Initialized to $controller->Auth->user() during startup().
	 *
	 * @var array
	 */
	protected $user;

	/**
	 * Stores the bound controller.
	 *
	 * @var Controller
	 */
	protected $controller = null;

	/**
	 * Method list for bound controller.
	 *
	 * @var array
	 */
	protected $methods = array();

	/**
	 * Stores the bound authentication object.
	 *
	 * @var BaseAuthenticate
	 * @see StatelessAuthComponent::constructAuthenticate()
	 */
	protected $authenticateObject = null;

	/**
	 * Stores the bound authorization object.
	 *
	 * @var BaseAuthorize
	 * @see StatelessAuthComponent::constructAuthorize()
	 */
	protected $authorizeObject = null;

	/**
	 * Initializes StatelessAuthComponent for use in the controller.
	 *
	 * Called before the Controller::beforeFilter().
	 *
	 * @param Controller $controller A reference to the instantiating controller object
	 * @return void
	 * @throws NotImplementedException If the current controller does not define a `::$privilege` property.
	 */
	public function initialize(Controller $controller) {
		parent::initialize($controller);
		$this->controller = $controller;

		$this->request = $controller->request;
		$this->response = $controller->response;
		$this->methods = $controller->methods;
		$this->action = $controller->request->params['action'];
		$this->constructAuthenticate();
		$this->constructAuthorize();
	}

	/**
	 * Component startup. Main execution method.
	 *
	 * Called after the Controller::beforeFilter() and before the controller
	 * action.
	 *
	 * @param Controller $controller A reference to the instantiating controller object.
	 * @return bool True on successful startup. (Always true.)
	 */
	public function startup(Controller $controller) {
		parent::startup($controller);

		$methods = array_flip(array_map('strtolower', $controller->methods));
		$action = strtolower($controller->request->params['action']);

		$isMissingAction = (
			$controller->scaffold === false &&
			!isset($methods[$action])
		);

		if ($isMissingAction) {
			return true;
		}

		if ($this->isAllowed($controller)) {
			return true;
		}

		if (!$this->getUser()) {
			return false;
		}

		if (
			$this->isLoginAction($controller) ||
			$this->isAuthorized($this->user())
		) {
			return true;
		}

		return false;
	}

	/**
	 * Get the current user stored in the Component.
	 *
	 * Returns a specific property from the user record if $key is provided.
	 *
	 * @param string $key Field to retrieve. Leave null to get entire User record.
	 * @return mixed User record, User field value, or null if no User is logged in.
	 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#accessing-the-logged-in-user
	 */
	public function user($key = null) {
		if (empty($this->user)) {
			return null;
		}
		if ($key === null) {
			return $this->user;
		}
		return Hash::get($this->user, $key);
	}

	/**
	 * Log a user in.
	 *
	 * If a $user is provided that data will be stored as the logged in
	 * user. If `$user` is empty or not specified, the request will be used
	 * to identify a user. If the identification was successful, the user
	 * record is stored in ::$user.
	 *
	 * @param array $user Either an array of user data, or null to identify a user using the current request.
	 * @return bool True on login success, false on failure
	 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#identifying-users-and-logging-them-in
	 */
	public function login($user = null) {
		if (empty($user)) {
			$user = $this->identify($this->request, $this->response);
		}

		if ($user) {
			$this->user = $user;
		}
		return (bool)$this->user();
	}

	/**
	 * Use the configured authentication adapters, and attempt to identify the user
	 * by credentials contained in $request.
	 *
	 * @param CakeRequest $request The request that contains authentication data.
	 * @param CakeResponse $response The response
	 * @return array User record data, or false, if the user could not be identified.
	 */
	public function identify(CakeRequest $request, CakeResponse $response) {
		$result = $this->authenticateObject->authenticate($request, $response);
		if (!empty($result) && is_array($result)) {
			return $result;
		}
		return false;
	}

	/**
	 * Log a user out.
	 *
	 * Just a shim to call the logut() method on the bound authentication object.
	 *
	 * @return string AuthComponent::$logoutRedirect
	 * @see AuthComponent::$logoutRedirect
	 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#logging-users-out
	 */
	public function logout() {
		$user = $this->user();
		$result = $this->authenticateObject->logout($user);
		return $result;
	}

	/**
	 * Takes a list of actions in the current controller for which authentication is not required, or
	 * no parameters to allow all actions.
	 *
	 * You can use allow with either an array, or var args.
	 *
	 * `$this->Auth->allow(array('edit', 'add'));` or
	 * `$this->Auth->allow('edit', 'add');` or
	 * `$this->Auth->allow();` to allow all actions
	 *
	 * @param string|array $action Controller action name or array of actions
	 * @return void
	 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#making-actions-public
	 */
	public function allow($action = null) {
		$args = func_get_args();
		if (empty($args) || $action === null) {
			$this->allowedActions = $this->methods;
			return;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		$this->allowedActions = array_merge($this->allowedActions, $args);
	}

	/**
	 * Removes items from the list of allowed/no authentication required actions.
	 *
	 * You can use deny with either an array, or var args.
	 *
	 * `$this->Auth->deny(array('edit', 'add'));` or
	 * `$this->Auth->deny('edit', 'add');` or
	 * `$this->Auth->deny();` to remove all items from the allowed list
	 *
	 * @param string|array $action Controller action name or array of actions
	 * @return void
	 * @see AuthComponent::allow()
	 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#making-actions-require-authorization
	 */
	public function deny($action = null) {
		$args = func_get_args();
		if (empty($args) || $action === null) {
			$this->allowedActions = array();
			return;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		foreach ($args as $arg) {
			$i = array_search($arg, $this->allowedActions);
			if (is_int($i)) {
				unset($this->allowedActions[$i]);
			}
		}
		$this->allowedActions = array_values($this->allowedActions);
	}

	/**
	 * Loads the configured authentication object.
	 *
	 * @return mixed An instance of the loaded object.
	 * @throws CakeException
	 */
	public function constructAuthenticate() {
		return $this->constructAuthObject('authenticate', 'Authentication');
	}

	/**
	 * Loads the configured authorization object.
	 *
	 * @return mixed An instance of the loaded object.
	 * @throws CakeException
	 */
	public function constructAuthorize() {
		return $this->constructAuthObject('authorize', 'Authorization');
	}

	/**
	 * Loads the configured authenticate/authorize object based on the provided key name.
	 *
	 * @param string $key Either 'authorize' or 'authenticate'.
	 * @param string $msgFragment an auth message fragment to include if throwing an Exception
	 * @return mixed An instance of the loaded object.
	 * @throws CakeException
	 */
	protected function constructAuthObject($key, $msgFragment) {
		if (empty($this->{$key})) {
			return;
		}

		$settings = $this->{$key};
		if (!empty($settings['className'])) {
			$class = $settings['className'];
			unset($settings['className']);
		}

		list($plugin, $class) = pluginSplit($class, true);
		$className = $class . Inflector::classify($key);
		App::uses($className, $plugin . 'Controller/Component/Auth');
		if (!class_exists($className)) {
			throw new CakeException(__d('cake_dev', '%s adapter "%s" was not found.', $msgFragment, $class));
		}
		if (!method_exists($className, $key)) {
			throw new CakeException(__d('cake_dev', '%s object must implement an %s() method.', $msgFragment, $key));
		}
		$propertyName = $key . 'Object';
		$this->{$propertyName} = new $className($this->_Collection, $settings);

		return $this->{$propertyName};
	}

	/**
	 * Check if the provided user is authorized for the request.
	 *
	 * Uses the configured Authorization adapters to check whether or not a user is authorized.
	 * Each adapter will be checked in sequence, if any of them return true, then the user will
	 * be authorized for the request.
	 *
	 * @param array $user The user to check the authorization of. If empty the user in the session will be used.
	 * @param CakeRequest $request The request to authenticate for. If empty, the current request will be used.
	 * @return bool True if $user is authorized, otherwise false
	 */
	public function isAuthorized($user = null, CakeRequest $request = null) {
		if (empty($user) && !$this->user()) {
			return false;
		}
		if (empty($user)) {
			$user = $this->user();
		}
		if (empty($request)) {
			$request = $this->request;
		}
		if ($this->authorizeObject->authorize($user, $request) === true) {
			return true;
		}
		return false;
	}

	/**
	 * Checks whether current action is accessible without authentication.
	 *
	 * @param Controller $controller A reference to the instantiating controller object
	 * @return bool True if action is accessible without authentication else false
	 */
	protected function isAllowed(Controller $controller) {
		$action = strtolower($controller->request->params['action']);
		if (in_array($action, array_map('strtolower', $this->allowedActions))) {
			return true;
		}
		return false;
	}

	/**
	 * Getter for the "correct" User record to use.
	 *
	 * If the provided argument is null, the configured ::user will
	 * be used. If that is also null, an exception is thrown. This is used
	 * in all of the convenience functions provided by this Component to validate arguments.
	 *
	 * @param array $user User array as provided by AuthComponent::user().
	 * @throws RuntimeException When both $user and ::$defaultuser are null.
	 * @return array
	 */
	protected function whichUser($user) {
		if (is_null($user)) {
			$user = $this->user;
		}
		return $user;
	}

	/**
	 * Sets the loaded::$user property from the authenticate object if not already set.
	 *
	 * Calls the ::$authenticate object's ::getUser() method to fetch the
	 * User using the available Request information.
	 *
	 * @return bool True if a user can be found, false if one cannot.
	 */
	protected function getUser() {
		$user = $this->user();
		if ($user) {
			return true;
		}

		$result = $this->authenticateObject->getUser($this->request);
		if (!empty($result) && is_array($result)) {
			$this->user = $result;
			return true;
		}

		return false;
	}

	/**
	 * Normalizes $loginAction and checks if current request URL is same as login action.
	 *
	 * @param Controller $controller A reference to the controller object.
	 * @return bool True if current action is login action else false.
	 */
	protected function isLoginAction(Controller $controller) {
		$url = '';
		if (isset($controller->request->url)) {
			$url = $controller->request->url;
		}
		$url = Router::normalize($url);
		$loginAction = Router::normalize($this->loginAction);

		return $loginAction === $url;
	}
}
