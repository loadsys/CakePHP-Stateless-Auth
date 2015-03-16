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
App::uses('FormAuthenticate', 'Controller/Component/Auth');

/**
 * TokenAuthenticate
 */
class TokenAuthenticate extends FormAuthenticate {

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
		$fields = $this->settings['fields'];

		// Add a surrounding [User] array, if not present.
		if (isset($request->data[$fields['username']])) {
			$request->data = array($model => $request->data);
		}

		// Check the fields.
		if (!$this->_checkFields($request, $model, $fields)) {
			return false;
		}

		// Call the login method.
		$user = $this->getModel()->login(
			$request->data[$model][$fields['username']],
			$request->data[$model][$fields['password']]
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
		$userModel = $this->getModel();
		if ($this->isActualClassMethod('logout', $userModel)) {
			return $userModel->logout($user);
		} else {
			return true;
		}
	}

	/**
	 * Get a User based on information in the request. Used by stateless authentication
	 * systems like basic and digest auth. Powers Auth->user() in a stateless system.
	 *
	 * @param CakeRequest $request Request object.
	 * @return mixed Either false or an array of user information
	 * @throws UnauthorizedJsonApiException If there is no HTTP_AUTHORIZATION header present, or an unexpired User session could not be retrieve using it.
	 */
	public function getUser(CakeRequest $request) {
		$token = $this->getToken($request);
		$UserModel = $this->getModel();
		$user = $UserModel->findForToken($token);

		if (empty($user['User'])) {
			throw new UnauthorizedJsonApiException('Missing, invalid or expired token present in request. Include an HTTP_AUTHORIZATION header, or please login to obtain a token.');
		}

		$UserModel->updateLastLogin($user['User']['id']);
		return (!empty($user['User']) ? $user['User'] : false);
	}

	/**
	 * Accessor to the User model object specified in settings.
	 *
	 * @return Model|false
	 */
	public function getModel() {
		return ClassRegistry::init($this->settings['userModel']);
	}

	/**
	 * Try to get the HTTP_AUTHORIZATION header value from the request.
	 *
	 * Permitted values may optionally be prefixed by `Bearer `.
	 *
	 * @param CakeRequest $request Request object.
	 * @return string The (possibly empty) string representing the provided auth token.
	 */
	public function getToken(CakeRequest $request) {
		$token = str_ireplace('Bearer ', '', $request->header('Authorization'));
		return $token;
	}

	/**
	 * Confirm that a method is actually defined (and not shadowed by
	 * `__call()` for the given object.)
	 *
	 * @access	protected
	 * @param	string	$method	The name of the method to check.
	 * @param	object	$obj	The instantiated object to check.
	 * @return	boolean			True if the named method exists (not via __call()), false otherwise.
	 */
	protected function isActualClassMethod($method, $obj) {
		return (
			in_array($method, get_class_methods($obj))
			&& is_callable(array($obj, $method))
		);
	}

}
