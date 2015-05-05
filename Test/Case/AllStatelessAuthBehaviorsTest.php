<?php
/*
 * Custom test suite to execute all StatelessAuth Plugin behavior tests.
 */
class AllStatelessAuthBehaviorsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All StatelessAuth Plugin Behavior Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Model/Behavior/');
		return $suite;
	}
}
