<?php
/*
 * Custom test suite to execute all StatelessAuth Plugin helper tests.
 */
class AllStatelessAuthHelpersTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All StatelessAuth Plugin Helper Tests');
		$suite->addTestDirectory(dirname(__FILE__) . '/View/Helper/');
		return $suite;
	}
}
