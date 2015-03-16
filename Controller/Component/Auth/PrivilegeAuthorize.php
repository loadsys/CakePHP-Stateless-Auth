<?php
/**
 * Provides methods to check access control information for the User that
 * was authenticated on the current request.
 *
 * @package		  StatelessAuth.Controller.Component.Auth
 */
App::uses('BaseAuthorize', 'Controller/Component/Auth');

/**
 * PrivilegeAuthorize object.
 *
 * Usage: In your [App]Controller's `$components` property, add the following.
 *
 * 	public $components = array(
 * 		'Auth' => array(
 * 			'authorize' => array(
 * 				'Privilege',
 * 				// (Or with overridden settings.)
 * 				'Privilege' => array(
 * 					'defaultReadActions' => array('index', 'view'),
 * 					'defaultWriteActions' => array('index', 'view', 'add', 'edit', 'delete'),
 *  			),
 * 			),
 * 		),
 * 	);
 *
 */
class PrivilegeAuthorize extends BaseAuthorize {

	/**
	 * Settings for this authorize object.
	 *
	 * - `defaultReadActions` - This object's default "read" controller actions.
	 * - `defaultWriteActions` - This object's default "write" controller actions.
	 *
	 * These are used if the attached ::$_Controller does not define ::readActions()
	 * or ::writeActions() methods that return a flat array of action names.
	 *
	 * Can be set when the Component is included in a Controller's ::$components['Auth']
	 * property.
	 *
	 * Note that readable actions are typically included as writeable actions
	 * as well. (A User than can `edit` and `delete` is assumed to also be able
	 * to `view`.)
	 *
	 * @var array
	 */
	public $settings = array(
		'defaultReadActions' => array('index', 'view'),
		'defaultWriteActions' => array('index', 'view', 'add', 'edit', 'delete'),
	);

	/**
	 * Enforces access control to various stacks in the app.
	 *
	 * Since it's possible for a controller to not "line up" exactly with
	 * the Privileges that are defined in the database and attached to Users,
	 * authorization can be overriden by defining the standard
	 * `isAuthorized()` method in a Controller. This will be called if found
	 * to be present, and is expected to return true to indicate the User is
	 * allowed to access the resource and throw a
	 * `ForbiddenByPermissionsException` exception to indicate access being
	 * denied or failure to authorize. If the function returns boolean false,
	 * this exception will be thrown for you.
	 *
	 * Summary of access rules:
	 *
	 *   - admin:read = Equivalent to *:read, the User has READ permission
	 *     to all sections, write access is still governed by individual
	 *     section Permissions.
	 *   - admin:write = Equivalent to *:write, the User has WRITE permission
	 *     to all sections. If this is set for the current User, no action
	 *     will be blocked.
	 *   - Remaining Permissions are checked by comparing the `::$privilege`
	 *     property of the requested Controller to the Permission granted to
	 *     the current User. The action is compared against the lists provided
	 *     by `::readActions()` and `::writeActions()`.
	 *
	 * Example:
	 *
	 *   - URL => /labs/add
	 *   - [User][Permission][lab] => write
	 *   - LabsController::$privilege => 'lab'
	 *   - Result => Access allowed.
	 *
	 * It's also worth nothing that although Cake automatically invokes this
	 * method in the normal dispatch flow, we short-circuit normal execution
	 * in all "access denied" cases and throw an Exception where normally we
	 * would just return `false`. This is because we don't want Cake's normal
	 * behavior of redirecting the client to the "login" page, but instead
	 * want to statelessly fail the request in a way the API client will
	 * understand.
	 *
	 * @param array $user An array containing a User record, typically provided by Auth->user().
	 * @param CakeRequest $request The active HTTP request object.
	 * @throws NotImplementedException If the controller does not define a valid ::$privilege property.
	 * @throws ForbiddenByPermissionsException If authorization fails (and debugging is on.)
	 * @throws NotFoundException If authorization fails (and debugging is off.)
	 * @return bool	True if the current User has Permission to access the requested section of the app, false otherwise.
	 */
	public function authorize($user, CakeRequest $request) {
		if (method_exists($this->_Controller, 'isAuthorized')) {
			if (!call_user_func(array($this->_Controller, 'isAuthorized'), $user)) {
				throw new ForbiddenByPermissionsException(null, 'Custom isAuthorized() method denied access.');
			} else {
				return true;
			}
		}

		$action = $request->params['action'] ?: false;

		$privilege = $this->_Controller->privilege;
		if (empty($privilege)) {
			throw new NotImplementedException(sprintf("%sController does not define the mandatory `::\$privilege` property used for authorization. See AppController::\$privilege for further explanation.", Inflector::classify($request->params['controller'])));
		}

		// If we somehow end up with an extra layer, knock it out of the way.
		if (isset($user['User']['Permission'])) {
			$user = $user['User'];
		}
		if (empty($user['Permission'])) {
			throw new ForbiddenByPermissionsException(null, 'No User Permission object available.');
		}
		$permissions = $user['Permission'];

		if (!$this->userHasAccess($permissions, $privilege, $action)) {
			if (Configure::read('debug') > 0) {
				throw new ForbiddenByPermissionsException();
			} else {
				throw new NotFoundException(); // **DO NOT** include a message that would differentiate this case from other 404's!
			}
		}

		return true;
	}

	/**
	 * Returns true when the User has access to the requested controller and action.
	 *
	 * @param array $permissions Array from the User of [Privilege.slug => Permission.level] pairs to check against.
	 * @param string $privilege The Privilege.slug in question for the current request.
	 * @param string $action The controller action being checked.
	 * @return bool True if the current User has read access to the requested action.
	 */
	public function userHasAccess($permissions, $privilege, $action) {
		if ($this->userCanRead($permissions, $privilege) && $this->isReadAction($action)) {
			return true;
		}
		if ($this->userCanWrite($permissions, $privilege) && $this->isWriteAction($action)) {
			return true;
		}
		return false;
	}

	/**
	 * Returns true when the User has read-level access to the requested
	 * section of the app. (User's with write access are also considered to
	 * have read access.)
	 *
	 * @param array $permissions Array from the User of [Privilege.slug => Permission.level] pairs to check against.
	 * @param string $privilege The Privilege.slug in question for the current request.
	 * @return bool True if the current User has read (or write) access to the requested privilege.
	 */
	public function userCanRead($permissions, $privilege) {
		$access = $this->userAccessForPrivilege($permissions, $privilege);
		return ($access === 'write' || $access === 'read');
	}

	/**
	 * Returns true when the User has write-level access to the requested
	 * section of the app.
	 *
	 * @param array $permissions Array from the User of [Privilege.slug => Permission.level] pairs to check against.
	 * @param string $privilege The Privilege.slug in question for the current request.
	 * @return bool True if the current User has write access to the requested privilege.
	 */
	public function userCanWrite($permissions, $privilege) {
		$access = $this->userAccessForPrivilege($permissions, $privilege);
		return ($access === 'write');
	}

	/**
	 * Returns true if the requested $action is listed by the Controller
	 * as a "read" action.
	 *
	 * @param string $action The controller action being checked.
	 * @return bool True if $action exists in the Controller's ::readActions() list.
	 */
	public function isReadAction($action) {
		if (
			method_exists($this->_Controller, 'readActions')
			&& is_array($this->_Controller->readActions())
		) {
			$readActions = $this->_Controller->readActions();
		} else {
			$readActions = $this->settings['defaultReadActions'];
		}
		return in_array($action, $readActions);
	}

	/**
	 * Returns true if the requested $action is listed by the Controller
	 * as a "write" action. All readActions are (typically) counted as
	 * writeActions as well. (Meaning Users with `write` access can also
	 * get to `read` actions.) Write actions are usually a superset of
	 * the read actions.
	 *
	 * @param string $action The controller action being checked.
	 * @return bool True if $action exists in the Controller's ::writeActions() list.
	 */
	public function isWriteAction($action) {
		if (
			method_exists($this->_Controller, 'writeActions')
			&& is_array($this->_Controller->writeActions())
		) {
			$writeActions = $this->_Controller->writeActions();
		} else {
			$writeActions = $this->settings['defaultWriteActions'];
		}
		return in_array($action, $writeActions);
	}

	/**
	 * Returns the access slug (write|read|none) for the requested
	 * Privilege.slug value using the provided Permissions array.
	 *
	 * Will use the User's `admin` privilege level if it grants higher
	 * access than the section-specific privilege does.
	 *
	 * Returns false if the requested Privilege slug does not exist or if
	 * Auth->user() isn't able to provide a record. Takes admin access into
	 * account and will return that first if set "above" the `none` level.
	 *
	 * @param array $permissions An array of [slug => write/read/none] pairs for a given User.
	 * @param string $privilege The Privilege.slug for which to obtain the User's access level.
	 * @return string|false The Permission level slug (write/read/none) granted by the provided Permission set for the requested Privilege. False on lookup failure.
	 */
	public function userAccessForPrivilege($permissions, $privilege) {
		if (!is_array($permissions)) {
			return false;
		}
		if (!isset($permissions[$privilege])) {
			return false;
		}
		if (isset($permissions['admin'])) {
			// This avoids the case where admin=read but section=write. The
			// specific section permission should "bump the user up" from
			// generic read-everywhere access to write access for the specific
			// section (by dropping past this return.)
			return $this->greaterPrivilege($permissions['admin'], $permissions[$privilege]);
		}
		return $permissions[$privilege];
	}

	/**
	 * Returns the "greater" of the two provided privilege level strings.
	 *
	 * Inputs must consist of (write|read|none), and are considered with
	 * that order of preference. See the unit tests for examples. Typically
	 * used to compare a section-specific access level against a User's admin
	 * access level to determine which one grants more access.
	 *
	 * @param string $left /write|read|none/
	 * @param string $right /write|read|none/
	 * @return string The "higher" access between the two.
	 */
	public function greaterPrivilege($left, $right) {
		$both = array($left, $right);
		if (in_array('write', $both, true)) {
			return 'write';
		}
		if (in_array('read', $both, true)) {
			return 'read';
		}
		return 'none';
	}
}
