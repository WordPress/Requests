<?php

namespace WpOrg\Requests\Tests\Utility\InputValidator;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\InputValidator;

/**
 * @covers \WpOrg\Requests\Utility\InputValidator::is_iterable
 */
final class IsIterableTest extends TestCase {

	/**
	 * Test whether a received input parameter is correctly identified as "iterable".
	 *
	 * @dataProvider dataValid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testValid($input) {
		$this->assertTrue(InputValidator::is_iterable($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataValid() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_ITERABLE);
	}

	/**
	 * Test whether a received input parameter is correctly identified as NOT "iterable".
	 *
	 * @dataProvider dataInvalid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testInvalid($input) {
		$this->assertFalse(InputValidator::is_iterable($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalid() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ITERABLE);
	}
}
