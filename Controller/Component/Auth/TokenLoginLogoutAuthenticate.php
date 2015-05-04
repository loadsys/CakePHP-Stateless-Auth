<?php
/**
 * Authenticate a user based on the request information.
 *
 * Uses the HTTP_AUTHORIZATION header to look up an associated User record
 * by `access_token` and `last_login_at` fields. Since the system is
 * stateless and no Session is maintained between requests, Auth->user()
 * is set to the resulting User automatically to make it available to the
 * rest of the Cake app like "normal".
 *
 * @package StatelessAuth.Controller.Component.Auth
 */
App::uses('TokenAuthenticate', 'StatelessAuth.Controller/Component/Auth');

/**
 * TokenAuthenticate
 */
class TokenLoginLogoutAuthenticate extends TokenAuthenticate {

	/**
	 * Settings for this object.
	 *
	 * - `fields` The fields to use to identify a user.
	 * - `userModel` The model name to use to look up User records, defaults to User.
	 * - `scope` Additional conditions to use when looking up and authenticating users,
	 *    i.e. `array('User.is_active' => 1).`
	 * - `recursive` The value of the recursive key passed to find(). Defaults to 0.
	 * - `contain` Extra models to contain and return with the User.
	 *
	 * @var array
	 */
	public $settings = array(
		'fields' => array(
			'username' => 'username',
			'password' => 'password',
			'token' => 'token',
		),
		'userModel' => 'User',
		'userFields' => null,
		'scope' => array(),
		'recursive' => 0,
		'contain' => array(),
		'header' => 'Authorization',
	);

	/**
	 * Stores a copy of the User model defined by ::$settings[userModel].
	 *
	 * @var Model
	 */
	protected $UserModel = null;

	/**
	 * Constructor
	 *
	 * @param ComponentCollection $collection The Component collection used on this request.
	 * @param array $settings Array of settings to use.
	 */
	public function __construct(ComponentCollection $collection, $settings) {
		parent::__construct($collection, $settings);

		$this->UserModel = $this->getModel();
		$this->requireUserModelMethods();
	}

	/**
	 * Authenticate a user based on the request information.
	 *
	 * Called only by Auth->login() to validate the User. The returned User
	 * array must contain the authentication token that was generated so it
	 * can be passed back to the client for future user.
	 *
	 * There are also two side effects:
	 *   1. The password hash stored in the DB for the User is updated from
	 *      SHA1 to Blowfish (if necessary).
	 *   2. The last_login_at and token fields are written back into the
	 *      User model record.
	 *
	 * @param CakeRequest $request Request to get authentication information from.
	 * @param CakeResponse $response A response object that can have headers added.
	 * @return mixed Either false on failure, or an array of user data on success.
	 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		$userModel = $this->settings['userModel'];
		list(, $model) = pluginSplit($userModel);
		$alias = $this->UserModel->alias;
		$fields = $this->settings['fields'];

		// Add a surrounding [User] array, if not present.
		if (isset($request->data[$fields['username']])) {
			$request->data = array($alias => $request->data);
		}

		// Check the fields.
		if (!$this->checkFields($request, $alias)) {
			return false;
		}

		// Call the login method.
		$user = $this->UserModel->login(
			$request->data[$alias][$fields['username']],
			$request->data[$alias][$fields['password']]
		);
		if (!$user) {
			return false;
		}

		return $user;
	}

	/**
	 * Allows you to hook into AuthComponent::logout(),
	 * and implement specialized logout behavior.
	 *
	 * All attached authentication objects will have this method
	 * called when a user logs out.
	 *
	 * @param array $user The user about to be logged out.
	 * @return void The return value from this method isn't checked by AuthComponent::logout().
	 */
	public function logout($user) {
		return $this->UserModel->logout($user);
	}

	/**
	 * Get a User based on information in the request. Used by stateless authentication
	 * systems like basic and digest auth. Powers Auth->user() in a stateless system.
	 *
	 * @param CakeRequest $request Request object.
	 * @return mixed Either false or an array of user information
	 * @throws StatelessAuthUnauthorizedException If there is no HTTP Authorization header present, or an unexpired User session could not be retrieve using it.
	 */
	public function getUser(CakeRequest $request) {
		$token = $this->getToken($request);
		$user = $this->UserModel->findForToken($token);
		$userModelName = $this->settings['userModel'];

		if (empty($user[$userModelName])) {
			throw new StatelessAuthUnauthorizedException(
				'Missing, invalid or expired token present in request. Include an HTTP_AUTHORIZATION header, or please login to obtain a token.'
			);
		}

		return $user[$userModelName];
	}

	/**
	 * Confirm that the supplied User model name defines the class methods
	 * required by this authenticator.
	 *
	 * @return void
	 * @throws StatelessAuthMissingMethodException If a required User model method is found to be missing.
	 */
	protected function requireUserModelMethods() {
		$requiredMethods = array(
			'login' => 'public function login($username, $password) => array|false',
			'logout' => 'public function logout($user) => bool',
			'findForToken' => 'public function findForToken($token) => array|false',
		);
		foreach ($requiredMethods as $method => $signature) {
			if (!$this->isActualClassMethod($method, $this->UserModel)) {
				throw new StatelessAuthMissingMethodException(
					"TokenLoginLogoutAuthenticate requires the {$this->settings['userModel']} model to define a `{$signature}` method." //@TODO: Add a note about using StatelessAuthBehavior once it exists.
				);
			}
		}
	}

	/**
	 * Checks the fields to ensure they are supplied.
	 *
	 * @param CakeRequest $request The request that contains login information.
	 * @param string $model The model used for login verification.
	 * @return bool False if the fields have not been supplied. True if they exist.
	 */
	protected function checkFields(CakeRequest $request, $model) {
		if (empty($request->data[$model])) {
			return false;
		}
		foreach (array($this->settings['fields']['username'], $this->settings['fields']['password']) as $field) {
			$value = $request->data($model . '.' . $field);
			if (empty($value) && $value !== '0' || !is_string($value)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Confirm that a method is actually defined (and not shadowed by
	 * `__call()` for the given object.)
	 *
	 * @param string $method The name of the method to check.
	 * @param object $obj The instantiated object to check.
	 * @return bool True if the named method exists (not via __call()), false otherwise.
	 */
	protected function isActualClassMethod($method, $obj) {
		return (
			in_array($method, get_class_methods($obj))
			&& is_callable(array($obj, $method))
		);
	}
}
