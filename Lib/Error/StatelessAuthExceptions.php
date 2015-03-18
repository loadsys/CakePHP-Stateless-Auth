<?php
/**
 * Custom Exceptions for the CakePHP Stateless Auth plugin.
 */

/**
 * StatelessAuthException
 *
 * Generic base exception for the plugin to extend.
 */
class StatelessAuthException extends CakeException {

	/**
	 * A short, human-readable summary of the problem. It SHOULD NOT change from
	 * occurrence to occurrence of the problem, except for purposes of
	 * localization.
	 *
	 * @var null
	 */
	public $title = 'Stateless Auth Exception';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var null
	 */
	public $detail = 'Stateless Auth Exception';

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var null
	 */
	public $code = 400;

	/**
	 * A URI that MAY yield further details about this particular occurrence
	 * of the problem.
	 *
	 * @var null
	 */
	public $href = null;

	/**
	 * A unique identifier for this particular occurrence of the problem.
	 *
	 * @var null
	 */
	public $id = null;

	/**
	 * The HTTP status code applicable to this problem, expressed as a string
	 * value.
	 *
	 * @var null
	 */
	public $status = null;

	/**
	 * Associated resources which can be dereferenced from the request document.
	 *
	 * @var null
	 */
	public $links = null;

	/**
	 * The relative path to the relevant attribute within the associated
	 * resource(s). Only appropriate for problems that apply to a single
	 * resource or type of resource.
	 *
	 * @var null
	 */
	public $path = null;

	/**
	 * Constructs a new instance of the base StatelessAuthException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Stateless Auth Exception',
		$detail = 'Stateless Auth Exception',
		$code = 400,
		$href = null,
		$id = null
	) {
		// Set the passed in properties to the properties of the Object
		$this->title = $title;
		$this->detail = $detail;
		$this->code = $code;
		$this->href = $href;
		$this->id = $id;
		parent::__construct($this->title, $code);
	}

	/**
	 * return the title for this Exception
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * return the detail for this Exception
	 *
	 * @return string
	 */
	public function getDetail() {
		return $this->detail;
	}

	/**
	 * return the href for this Exception
	 *
	 * @return string
	 */
	public function getHref() {
		return $this->href;
	}

	/**
	 * return the id for this Exception
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
}

/**
 * Used when an HTTP Authorization header token is not set, expired, or invalid.
 */
class StatelessAuthUnauthorizedException extends StatelessAuthException {

	/**
	 * Constructs a new instance of the StatelessAuthUnauthorizedException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Unauthorized Access',
		$detail = 'Unauthorized Access',
		$code = 401,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * Used when a User's Permissions forbid access to the requested section of
 * the app. @TODO: Leave in FM.
 */
class StatelessAuthForbiddenByPermissionsException extends StatelessAuthException {

	/**
	 * Constructs a new instance of the StatelessAuthForbiddenByPermissionsException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Unauthorized Access',
		$detail = 'Access to the requested resource is denied by the Permissions on your account.',
		$code = 403,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * Used when the named User model does not define necessary methods.
 */
class StatelessAuthMissingMethodException extends StatelessAuthException {

	/**
	 * Constructs a new instance of the StatelessAuthMissingMethodException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Missing Method',
		$detail = 'Missing Method',
		$code = 500,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

