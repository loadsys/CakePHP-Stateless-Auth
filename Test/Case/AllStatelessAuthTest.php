<?php
/*
 * Custom test suite to execute all StatelessAuth Plugin tests.
 */
class AllStatelessAuthTest extends PHPUnit_Framework_TestSuite {
	public static $suites = array(
		// Then data manipulation.
		'AllStatelessAuthBehaviorsTest.php',
		'AllStatelessAuthModelsTest.php',

		// Then business logic.
		'AllStatelessAuthComponentsTest.php',
		'AllStatelessAuthControllersTest.php',

		// Then view helpers.
		'AllStatelessAuthHelpersTest.php',
	);

	public static function suite() {
		$path = dirname(__FILE__) . '/';
		$suite = new CakeTestSuite('All Tests');

		foreach (self::$suites as $file) {
			if (is_readable($path . $file)) {
				$suite->addTestFile($path . $file);
			}
		}

		return $suite;
	}
}
