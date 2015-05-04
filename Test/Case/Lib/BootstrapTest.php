<?php

/**
 * StatelessAuthAppModel Test Case
 *
 */
class StatelessAuthBootstrapTest extends CakeTestCase {

	/**
	 * There is nothing to test. This just completes code coverage.
	 */
	public function testBootstrap() {
		require_once(APP.'Plugin/StatelessAuth/Config/bootstrap.php');
		$result = new StatelessAuthException('no op');
	}

}
