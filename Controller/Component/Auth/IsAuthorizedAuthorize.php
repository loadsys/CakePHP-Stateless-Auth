<?php
/**
 * Provides a basic way to delegate authorization checking to the
 * controllers via Controller::isAuthorized() methods.
 *
 * @package		  StatelessAuth.Controller.Component.Auth
 */
App::uses('BaseAuthorize', 'Controller/Component/Auth');

/**
 * IsAuthorizedAuthorize object.
 *
 * Usage: In your [App]Controller's `$components` property, add the following.
 *
 * 	public $components = array(
 * 		'Auth' => array(
 * 			'authorize' => array(
 * 				'IsAuthorized',
 * 			),
 * 		),
 * 	);
 *
 */
class IsAuthorizedAuthorize extends BaseAuthorize {

	/**
	 * Delegates the job of enforcing authorization to the active controller.
	 *
	 * When using this Authorize object, each Controller must define an
	 * `::isAuthorized($user)` method. An Exception will be thrown if such
	 * a method does not exist.
	 *
	 * The method takes as its only argument an array of User data as
	 * normally provided by `Auth->user()` to use in determining access.
	 *
	 * The method must return a boolean true/false value where true
	 * indicates the currently logged in User is allowed to access the
	 * current CakeRequest, and false indicates the User's access is denied.
	 *
	 * As an example, if you wanted all logged-in-users to have access to
	 * all parts of your app, you could add the following method to your
	 * AppController:
	 *
	 * 		public function isAuthorized($user) {
	 * 			return true;
	 * 		}
	 *
	 * @param array $user An array containing a User record, typically provided by Auth->user().
	 * @param CakeRequest $request The active HTTP request object.
	 * @throws NotImplementedException If the controller does not define a valid ::isAuthorized() method.
	 * @return bool	True if the current User has access to the requested section of the app, false otherwise.
	 */
	public function authorize($user, CakeRequest $request) {
		if (!$this->controllerIsAuthorizedExists()) {
			throw new NotImplementedException(sprintf("%sController does not define the mandatory `::isAuthorized()` method used for authorization.", Inflector::classify($request->params['controller'])));
		}

		return $this->delegateToIsAuthorized($user);
	}

	/**
	 * Encapsulate checking for the pressence of the Controller::isAuthorized() method.
	 *
	 * Allows it to be reused or modified in sub-classes.
	 *
	 * @return bool	True if the attached Controller defines an isAuthorized() method, false otherwise.
	 */
	protected function controllerIsAuthorizedExists() {
		return method_exists($this->_Controller, 'isAuthorized');
	}

	/**
	 * Encapsulate the delegation to the Controller::isAuthorized() method.
	 *
	 * Allows it to be reused or modified in sub-classes.
	 *
	 * @param array $user An array containing a User record, typically provided by Auth->user().
	 * @return bool	True if the current User has access to the requested section of the app, false otherwise.
	 */
	protected function delegateToIsAuthorized($user) {
		return call_user_func(array($this->_Controller, 'isAuthorized'), $user);
	}
}
