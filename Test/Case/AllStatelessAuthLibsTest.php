<?php
/*
 * Custom test suite to execute all StatelessAuth Plugin lib tests.
 */
class AllStatelessAuthLibsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All StatelessAuth Plugin Lib Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Lib/');
		return $suite;
	}
}
