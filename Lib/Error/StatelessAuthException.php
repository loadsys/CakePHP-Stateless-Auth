<?php
/**
 * Custom Exceptions for the CakePHP StatelessAuth Plugin
 *
 * @package StatelessAuth.Lib.Error
 */
App::uses('BaseSerializerException', 'SerializersErrors.Error');

/**
 * StatelessAuthException
 *
 * Generic base exception for the plugin to extend.
 */
class StatelessAuthException extends BaseSerializerException {

	/**
	 * Constructs a new instance of the base StatelessAuthException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Stateless Auth Exception',
		$detail = 'Stateless Auth Exception',
		$status = 400,
		$id = null,
		$href = null,
		$links = null,
		$paths = null
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * Used when an HTTP Authorization header token is not set, expired, or invalid.
 */
class StatelessAuthUnauthorizedException extends StatelessAuthException {

	/**
	 * Constructs a new instance of the StatelessAuthUnauthorizedException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Unauthorized Access',
		$detail = 'Unauthorized Access',
		$status = 401,
		$id = null,
		$href = null,
		$links = null,
		$paths = null
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
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
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Unauthorized Access',
		$detail = 'Access to the requested resource is denied by the Permissions on your account.',
		$status = 403,
		$id = null,
		$href = null,
		$links = null,
		$paths = null
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * Used when the named User model does not define necessary methods.
 */
class StatelessAuthMissingMethodException extends StatelessAuthException {

	/**
	 * Constructs a new instance of the StatelessAuthMissingMethodException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Missing Method',
		$detail = 'Missing Method',
		$status = 500,
		$id = null,
		$href = null,
		$links = null,
		$paths = null
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

