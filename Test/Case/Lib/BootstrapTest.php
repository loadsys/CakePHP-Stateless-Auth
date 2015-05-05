<?php
/**
 * Verify StatelessAuth Bootstrap works correctly
 *
 * @package StatelessAuth.Test.Case.Lib
 */

/**
 * StatelessAuthBootstrapTest
 */
class StatelessAuthBootstrapTest extends CakeTestCase {

	/**
	 * There is nothing to test. This just completes code coverage.
	 */
	public function testBootstrap() {
		require_once APP . 'Plugin' . DS . 'SerializersErrors' . DS . 'Config' . DS . 'bootstrap.php';
		$statelessAuthException = new StatelessAuthException("New StatelessAuthException");
		$baseSerializerException = new BaseSerializerException("New BaseSerializerException");
	}

}
