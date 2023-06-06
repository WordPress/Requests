<?php

namespace WpOrg\Requests\Tests\Requests;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Requests::get_certificate_path
 * @covers \WpOrg\Requests\Requests::set_certificate_path
 */
final class SetCertificatePathTest extends TestCase {

	/**
	 * Tests receiving an exception when an invalid input type is passed as `$path` to the set_certificate_path() method.
	 *
	 * @dataProvider dataInvalidData
	 *
	 * @param mixed $input Invalid input.
	 *
	 * @return void
	 */
	public function testInvalidData($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($path) must be of type string|Stringable|bool');

		Requests::set_certificate_path($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidData() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_BOOL, TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Tests setting a custom certificate path with valid data types (though potentially not a valid path).
	 *
	 * @dataProvider dataValidData
	 *
	 * @param mixed $input Valid input.
	 *
	 * @return void
	 */
	public function testValidData($input) {
		Requests::set_certificate_path($input);

		$this->assertSame($input, Requests::get_certificate_path());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataValidData() {
		return [
			'boolean false'     => [false],
			'boolean true'      => [true],
			'string'            => ['path/to/file.pem'],
			'stringable object' => [new StringableObject('path/to/file.pem')],
		];
	}
}
