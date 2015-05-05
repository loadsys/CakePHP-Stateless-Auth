<?php
/**
 * StatelessAuthAppController test file
 */
App::uses('StatelessAuthAppController', 'StatelessAuth.Controller');

/**
 * StatelessAuthAppControllerTest class
 *
 * @package       Cake.Test.Case.Controller
 */
class StatelessAuthAppControllerTest extends ControllerTestCase {

	/**
	 * There is nothing to test. This just completes code coverage.
	 */
	public function testNothing() {
		$controller = $this->generate('StatelessAuth.StatelessAuthApp');
		$result = $controller->beforeFilter();
	}

}
