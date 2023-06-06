<?php

namespace WpOrg\Requests\Tests\Utility\InputValidator;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\InputValidator;

/**
 * @covers \WpOrg\Requests\Utility\InputValidator::has_array_access
 */
final class HasArrayAccessTest extends TestCase {

	/**
	 * Test whether a received input parameter is correctly identified as "accessible as array".
	 *
	 * @dataProvider dataValid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testValid($input) {
		$this->assertTrue(InputValidator::has_array_access($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataValid() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
	}

	/**
	 * Test whether a received input parameter is correctly identified as NOT "accessible as array".
	 *
	 * @dataProvider dataInvalid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testInvalid($input) {
		$this->assertFalse(InputValidator::has_array_access($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalid() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
	}
}
