<?php
// test classes for mocking
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DS . "test_classes.php";

/**
 * Exceptions tests
 *
 */
class StatelessAuthExceptionsTest extends CakeTestCase {

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Confirm that all Exception constructors set provided args into the
	 * correct properties. As a side-effect, also tests the getter methods
	 * for all properties.
	 *
	 * @param  string $class The Exception class name to instantiate.
	 * @param	array	$args An array of args to pass to the constructor method.
	 * @param  array $expected The expected properties of the Exception class
	 * @param  string $msg Optional PHPUnit error message when the assertion fails.
	 * @return void
	 * @dataProvider	provideTestConstructorsArgs
	 */
	public function testExceptionConstructors($class, $args, $expected, $msg = 'The method ::%1$s() is expected to return the value `%2$s`.') {
		extract($args);
		$e = new $class($title, $detail, $code, $href, $id);
		foreach ($expected as $method => $value) {
			if (is_array($value)) {
				$this->assertEquals(
					$value,
					$e->{$method}(),
					sprintf($msg, $method, implode($value, ", "))
				);
			} else {
				$this->assertEquals(
					$value,
					$e->{$method}(),
					sprintf($msg, $method, $value)
				);
			}
		}
	}

	/**
	 * Provide sets of [exception class name, constructor args, expected, msg] sets
	 * to testExceptionConstructors();
	 *
	 * @return array data inputs to testExceptionConstructors
	 */
	public function provideTestConstructorsArgs() {
		return array(
			array(
				'StatelessAuthException', // Exception class to instantiate.
				array( // Args to pass to constructor.
					'title' => 'Stateless Auth Exception',
					'detail' => 'Stateless Auth Exception',
					'code' => 400,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array( // Getter methods to run and expected values.
					'getTitle' => 'Stateless Auth Exception',
					'getDetail' => 'Stateless Auth Exception',
					'getCode' => 400,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				// optional phpunit assertion failed message here
			),

			array(
				'StatelessAuthException',
				array(
					'title' => 'a title',
					'detail' => 'some detail',
					'code' => 444,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'a title',
					'getDetail' => 'some detail',
					'getCode' => 444,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'StatelessAuthUnauthorizedException',
				array(
					'title' => 'Unauthorized Access',
					'detail' => 'Unauthorized Access',
					'code' => 401,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Unauthorized Access',
					'getCode' => 401,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'StatelessAuthForbiddenByPermissionsException',
				array(
					'title' => 'Unauthorized Access',
					'detail' => 'Access to the requested resource is denied by the Permissions on your account.',
					'code' => 403,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Access to the requested resource is denied by the Permissions on your account.',
					'getCode' => 403,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'StatelessAuthMissingMethodException',
				array(
					'title' => 'Missing Method',
					'detail' => 'Missing Method',
					'code' => 500,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Missing Method',
					'getDetail' => 'Missing Method',
					'getCode' => 500,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),
		);
	}

	/**
	 * Confirm that all Exception constructors set default args into the
	 * correct properties. As a side-effect, also tests the getter methods
	 * for all properties.
	 *
	 * @param  string $class The Exception class name to instantiate.
	 * @param  array $expected The expected properties of the Exception class
	 * @param  string $msg Optional PHPUnit error message when the assertion fails.
	 * @return void
	 * @dataProvider	provideTestConstructorsDefaultValues
	 */
	public function testExceptionConstructorsDefaultValues($class, $expected, $msg = 'The method ::%1$s() is expected to return the value `%2$s`.') {
		$e = new $class();
		foreach ($expected as $method => $value) {
			if (is_array($value)) {
				$this->assertEquals(
					$value,
					$e->{$method}(),
					sprintf($msg, $method, implode($value, ", "))
				);
			} else {
				$this->assertEquals(
					$value,
					$e->{$method}(),
					sprintf($msg, $method, $value)
				);
			}
		}
	}

	/**
	 * Provide sets of [exception class name, expected, msg] sets
	 * to testExceptionConstructorsDefaultValues();
	 *
	 * @return array data inputs to testExceptionConstructorsDefaultValues
	 */
	public function provideTestConstructorsDefaultValues() {
		return array(
			array(
				'StatelessAuthException', // Exception class to instantiate.
				array( // Getter methods to run and expected values.
					'getTitle' => 'Stateless Auth Exception',
					'getDetail' => 'Stateless Auth Exception',
					'getCode' => 400,
					'getHref' => null,
					'getId' => null,
				),
				// optional phpunit assertion failed message here
			),

			array(
				'StatelessAuthUnauthorizedException',
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Unauthorized Access',
					'getCode' => 401,
					'getHref' => null,
					'getId' => null,
				),
			),

			array(
				'StatelessAuthForbiddenByPermissionsException',
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Access to the requested resource is denied by the Permissions on your account.',
					'getCode' => 403,
					'getHref' => null,
					'getId' => null,
				),
			),

			array(
				'StatelessAuthMissingMethodException',
				array(
					'getTitle' => 'Missing Method',
					'getDetail' => 'Missing Method',
					'getCode' => 500,
					'getHref' => null,
					'getId' => null,
				),
			),
		);
	}
}