<?php
App::uses('StatelessAuthAppModel', 'StatelessAuth.Model');

/**
 * StatelessAuthAppModel Test Case
 *
 */
class StatelessAuthAppModelTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
	);

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->Model = ClassRegistry::init('StatelessAuth.StatelessAuthAppModel');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->Model);
		parent::tearDown();
	}

	/**
	 * There is nothing to test. This just completes code coverage.
	 */
	public function testNothing() {
		$result = $this->Model->beforeValidate();
	}

}
