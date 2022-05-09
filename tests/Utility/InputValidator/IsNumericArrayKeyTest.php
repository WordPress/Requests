<?php

namespace WpOrg\Requests\Tests\Utility\InputValidator;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\InputValidator;

/**
 * @covers \WpOrg\Requests\Utility\InputValidator::is_numeric_array_key
 */
final class IsNumericArrayKeyTest extends TestCase {

	/**
	 * Test whether a received input parameter is correctly identified as usable as a valid numeric (integer) array key.
	 *
	 * @dataProvider dataValid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testValid($input) {
		$this->assertTrue(InputValidator::is_numeric_array_key($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataValid() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_INT, ['numeric string']);
	}

	/**
	 * Test whether a received input parameter is correctly identified as NOT valid as a numeric (integer) array key.
	 *
	 * @dataProvider dataInvalid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testInvalid($input) {
		$this->assertFalse(InputValidator::is_numeric_array_key($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalid() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT, ['numeric string']);
	}
}
