<?php
/**
 * Tests the StandardJsonApiExceptions Classes to ensure it matches the expected
 * format
 *
 * @package StatelessAuth.Test.Case.Lib.Error
 */
App::import('Lib/Error', 'StatelessAuth.StatelessAuthException');

/**
 * StandardJsonApiExceptionsTest
 */
class StatelessAuthExceptionTest extends CakeTestCase {

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
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testStatelessAuthExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new StatelessAuthException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('StatelessAuthException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title(),
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail(),
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status(),
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id(),
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href(),
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links(),
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths(),
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testStatelessAuthUnauthorizedExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new StatelessAuthUnauthorizedException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('StatelessAuthUnauthorizedException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title(),
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail(),
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status(),
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id(),
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href(),
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links(),
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths(),
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testStatelessAuthForbiddenByPermissionsExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new StatelessAuthForbiddenByPermissionsException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('StatelessAuthForbiddenByPermissionsException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title(),
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail(),
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status(),
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id(),
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href(),
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links(),
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths(),
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testStatelessAuthMissingMethodExceptionConstructor() {
		$title = "New Title";
		$detail = array("something" => "Custom detail message");
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new StatelessAuthMissingMethodException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('StatelessAuthMissingMethodException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title(),
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail(),
			"The exception `detail` property does not match what we passed"
		);
		$this->assertEquals(
			$status,
			$exception->status(),
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id(),
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href(),
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links(),
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths(),
			"Paths does not match expectation"
		);
	}

}
