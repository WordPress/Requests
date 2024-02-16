<?php

namespace WpOrg\Requests\Tests\Utility\HttpStatus;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\HttpStatus;

/**
 * @covers \WpOrg\Requests\Utility\HttpStatus::get_text
 */
class GetTextTest extends TestCase {
	/**
	 * Test retrieving a valid status code.
	 *
	 * @dataProvider dataAccessValidEntry
	 *
	 * @param mixed $code Status code value to test.
	 *
	 * @return void
	 */
	public function testAccessValidEntry($code, $text) {
		$this->assertSame($text, HttpStatus::get_text($code));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataAccessValidEntry() {
		return [
			'integer key' => [404, 'Not Found'],
			'string key'  => ['502', 'Bad Gateway'],
		];
	}

	/**
	 * Test retrieving a status code with an invalid type.
	 *
	 * @dataProvider dataAccessInvalidType
	 *
	 * @param mixed $code Status code value to test.
	 *
	 * @return void
	 */
	public function testAccessInvalidType($code) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('a valid HTTP status code as an int or numeric string');
		HttpStatus::get_text($code);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataAccessInvalidType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT, TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Test retrieving a status code with a matching type but an invalid code.
	 *
	 * @dataProvider dataAccessInvalidCode
	 *
	 * @param mixed $code Status code value to test.
	 *
	 * @return void
	 */
	public function testAccessInvalidCode($code) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage(
			sprintf(
				'a valid HTTP status code as an int or numeric string, %s (%s) given',
				gettype($code),
				$code
			)
		);
		HttpStatus::get_text($code);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataAccessInvalidCode() {
		return [
			'negative integer' => [-1],
			'zero integer'     => [0],
			'too low integer'  => [42],
			'too high integer' => [1000],
			'negative string'  => ['-1'],
			'zero string'      => ['0'],
			'too low string'   => ['42'],
			'too high string'  => ['1000'],
			'PHP_INT_MAX'      => [PHP_INT_MAX],
		];
	}
}
