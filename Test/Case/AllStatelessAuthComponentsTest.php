<?php
/*
 * Custom test suite to execute all StatelessAuth Plugin component tests.
 */
class AllStatelessAuthComponentsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All StatelessAuth Plugin Component Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Controller/Component/');
		return $suite;
	}
}
