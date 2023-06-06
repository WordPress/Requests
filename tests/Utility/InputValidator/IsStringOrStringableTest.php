<?php

namespace WpOrg\Requests\Tests\Utility\InputValidator;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\InputValidator;

/**
 * @covers \WpOrg\Requests\Utility\InputValidator::is_string_or_stringable
 */
final class IsStringOrStringableTest extends TestCase {

	/**
	 * Test that a received input parameter is correctly identified as a valid string or "stringable".
	 *
	 * @dataProvider dataValid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testValid($input) {
		$this->assertTrue(InputValidator::is_string_or_stringable($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataValid() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Test that a received input parameter is correctly identified as NOT valid as a string or "stringable".
	 *
	 * @dataProvider dataInvalid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testInvalid($input) {
		$this->assertFalse(InputValidator::is_string_or_stringable($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalid() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRINGABLE);
	}
}
