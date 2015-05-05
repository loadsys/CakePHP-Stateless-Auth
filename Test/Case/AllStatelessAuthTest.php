<?php
/*
 * Custom test suite to execute all StatelessAuth Plugin tests.
 */
class AllStatelessAuthTest extends PHPUnit_Framework_TestSuite {

	public static $suites = array(
		'AllStatelessAuthLibsTest.php',
		'AllStatelessAuthBehaviorsTest.php',
		'AllStatelessAuthComponentsTest.php',
		'AllStatelessAuthModelsTest.php',
		'AllStatelessAuthControllersTest.php',
	);

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$path = dirname(__FILE__) . '/';
		$suite = new CakeTestSuite('All StatelessAuth Tests');

		foreach (self::$suites as $file) {
			if (is_readable($path . $file)) {
				$suite->addTestFile($path . $file);
			}
		}

		return $suite;
	}
}
