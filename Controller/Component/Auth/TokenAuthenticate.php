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
App::uses('BaseAuthenticate', 'Controller/Component/Auth');

/**
 * TokenAuthenticate
 */
class TokenAuthenticate extends BaseAuthenticate {

	/**
	 * Settings for this object.
	 *
	 * - `fields` The fields to use to identify a user.
	 * - `userModel` The model name to use to look up User records, defaults to User.
	 * - `userFields` Array of fields to return from the User record.
	 * - `scope` Additional conditions to use when looking up and authenticating users,
	 *    i.e. `array('User.is_active' => 1).`
	 * - `recursive` The value of the recursive key passed to find(). Defaults to 0.
	 * - `contain` Extra models to contain and return with the User.
	 *
	 * @var array
	 */
	public $settings = array(
		'fields' => array(
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
	 * Authenticate a user based on the request information alone.
	 *
	 * This method is triggered by a call to Auth->login(), which either
	 * sets the "current user" if User data was passed to it, or delegates
	 * to this method to authenticate a raw CakeRequest and return the
	 * correct User account if the request is found to be valid.
	 *
	 * In a stateful system, this method would check a POSTed
	 * username/password against the database and start a $_SESSION if a
	 * valid User record was found. From then on, a browser cookie is
	 * usually passed back to the server with each new request, which from
	 * then on acts the same as an Authorization HTTP header in a stateless
	 * system.
	 *
	 * In a stateless system like this one, there is no username and password
	 * to log in since there is no $_SESSION maintained between requests.
	 * *Every* request must contain the equivalent of a session cookie (the
	 * HTTP Authorization header.) Therefore a call to Auth->login() that
	 * doesn't contain a specific User record for use can do nothing
	 * different from authenticating a "normal" request and must simply
	 * check for the auth token in the request headers like getUser() does.
	 *
	 * In normal use, a Cake app using this component would never make a
	 * call to Auth->login() in this manner, using it only when it was
	 * necessary to change "the logged in user" mid-request by explicitly
	 * providing the User record the StatelessAuthComponent should use to
	 * Auth->login($user).
	 *
	 * @param CakeRequest $request Request to get authentication information from.
	 * @param CakeResponse $response A response object that can have headers added.
	 * @return array|false An array of user data on success, or false on failure.
	 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		try {
			$user = $this->getUser($request);
		} catch (Exception $e) {
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
		return true;
	}

	/**
	 * Get a User model record based on information in the request.
	 *
	 * Powers Auth->user() in a stateless system.
	 *
	 * @param CakeRequest $request Request object.
	 * @return array|false An array with the contents of the [User] record on success, false on failure.
	 * @throws StatelessAuthUnauthorizedException If there is no `Authorization` header present, or a User record could not be retrieved using it.
	 */
	public function getUser(CakeRequest $request) {
		$userModelName = $this->settings['userModel'];
		$user = $this->findUserForToken($this->getToken($request));
		if (empty($user[$userModelName])) {
			throw new StatelessAuthUnauthorizedException(
				'Missing, invalid or expired token present in request. Include an Authorization header.'
			);
		}
		return $user[$userModelName];
	}

	/**
	 * Accessor to the User model object specified in settings.
	 *
	 * @return Model|false
	 */
	public function getModel() {
		list(, $model) = pluginSplit($this->settings['userModel']);
		return ClassRegistry::init($model);
	}

	/**
	 * Try to get the HTTP `Authorization` header value from the request.
	 *
	 * Permitted values may optionally be prefixed by `Bearer ` or `Bearer: `.
	 *
	 * @param CakeRequest $request Request object.
	 * @return string The (possibly empty) string representing the provided auth token.
	 */
	public function getToken(CakeRequest $request) {
		$token = preg_replace('/^Bearer:?\s+/i', '', $request->header($this->settings['header']));
		return $token;
	}

	/**
	 * Attempt to fetch a User model record for the provided auth token.
	 *
	 * Uses this object's configuration settings to determine what model to
	 * query, what field to check against, and what additional conditions
	 * to impose upon the query.
	 *
	 * @param string $token The token obtained from the HTTP request headers to use to look up the User record.
	 * @return array|false An array containing the [User] record on success, false on failure.
	 */
	protected function findUserForToken($token) {
		$conditions = array(
			$this->settings['userModel'] . '.' . $this->settings['fields']['token'] => $token,
		);
		$conditions = array_merge($conditions, $this->settings['scope']);

		$options = array(
			'conditions' => $conditions,
			'recursive' => $this->settings['recursive'],
			'contain' => $this->settings['contain'],
		);

		if (
			is_array($this->settings['userFields'])
			&& count($this->settings['userFields'])
		) {
			$options['fields'] = $this->settings['userFields'];
		}

		$user = $this->getModel()->find('first', $options);
		return $user;
	}
}
