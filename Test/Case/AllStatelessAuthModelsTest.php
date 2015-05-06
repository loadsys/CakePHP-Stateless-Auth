<?php
/*
 * Custom test suite to execute all StatelessAuth Plugin model tests.
 */
class AllStatelessAuthModelsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All StatelessAuth Model Tests');
		$suite->addTestDirectory(dirname(__FILE__) . '/Model/');
		return $suite;
	}
}
