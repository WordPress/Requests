<?php

namespace WpOrg\Requests\Tests\Utility;

use ArrayIterator;
use stdClass;
use WpOrg\Requests\Tests\Fixtures\ArrayAccessibleObject;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\InputValidator;

/**
 * @coversDefaultClass \WpOrg\Requests\Utility\InputValidator
 */
final class InputValidatorTest extends TestCase {

	/**
	 * Curl handle.
	 *
	 * @var resource|\CurlHandle
	 */
	private static $curl_handle;

	/**
	 * Directory handle.
	 *
	 * @var resource
	 */
	private static $dir_handle;

	/**
	 * Clean up after the tests.
	 */
	public static function tear_down_after_test() {
		if (isset(self::$curl_handle) && is_resource(self::$curl_handle)) {
			curl_close(self::$curl_handle);
		}

		if (isset(self::$dir_handle)) {
			closedir(self::$dir_handle);
		}
	}

	/**
	 * Test that a received input parameter is correctly identified as string or "stringable".
	 *
	 * @dataProvider dataInput
	 *
	 * @covers ::is_string_or_stringable
	 *
	 * @param mixed $input    Input parameter to verify.
	 * @param array $expected Expected output for the respective functions.
	 *
	 * @return void
	 */
	public function testIsStringOrStringable($input, $expected) {
		$this->assertSame($expected['is_string_or_stringable'], InputValidator::is_string_or_stringable($input));
	}

	/**
	 * Test whether a received input parameter is correctly identified as usable as a numeric (integer) array key.
	 *
	 * @dataProvider dataInput
	 *
	 * @covers ::is_numeric_array_key
	 *
	 * @param mixed $input    Input parameter to verify.
	 * @param array $expected Expected output for the respective functions.
	 *
	 * @return void
	 */
	public function testIsNumericArrayKey($input, $expected) {
		$this->assertSame($expected['is_numeric_array_key'], InputValidator::is_numeric_array_key($input));
	}

	/**
	 * Test whether a received input parameter is correctly identified as "stringable".
	 *
	 * @dataProvider dataInput
	 *
	 * @covers ::is_stringable_object
	 *
	 * @param mixed $input    Input parameter to verify.
	 * @param array $expected Expected output for the respective functions.
	 *
	 * @return void
	 */
	public function testIsStringableObject($input, $expected) {
		$this->assertSame($expected['is_stringable_object'], InputValidator::is_stringable_object($input));
	}

	/**
	 * Test whether a received input parameter is correctly identified as "accessible as array".
	 *
	 * @dataProvider dataInput
	 *
	 * @covers ::has_array_access
	 *
	 * @param mixed $input    Input parameter to verify.
	 * @param array $expected Expected output for the respective functions.
	 *
	 * @return void
	 */
	public function testHasArrayAccess($input, $expected) {
		$this->assertSame($expected['has_array_access'], InputValidator::has_array_access($input));
	}

	/**
	 * Test whether a received input parameter is correctly identified as "iterable".
	 *
	 * @dataProvider dataInput
	 *
	 * @covers ::is_iterable
	 *
	 * @param mixed $input    Input parameter to verify.
	 * @param array $expected Expected output for the respective functions.
	 *
	 * @return void
	 */
	public function testIsIterable($input, $expected) {
		$this->assertSame($expected['is_iterable'], InputValidator::is_iterable($input));
	}

	/**
	 * Test whether a received input parameter is correctly identified as a Curl handle.
	 *
	 * @dataProvider dataInput
	 *
	 * @covers ::is_curl_handle
	 *
	 * @param mixed $input    Input parameter to verify.
	 * @param array $expected Expected output for the respective functions.
	 *
	 * @return void
	 */
	public function testIsCurlHandle($input, $expected) {
		$this->assertSame($expected['is_curl_handle'], InputValidator::is_curl_handle($input));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataInput() {
		if (isset(self::$curl_handle, self::$dir_handle) === false) {
			self::$curl_handle = curl_init('http://httpbin.org/anything');
			self::$dir_handle  = opendir(__DIR__);
		}

		return [
			'null' => [
				'input'    => null,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'boolean false' => [
				'input'    => false,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'boolean true' => [
				'input'    => true,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'integer 0' => [
				'input'    => 0,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => true,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'negative integer' => [
				'input'    => -123,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => true,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'positive integer' => [
				'input'    => 786687,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => true,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'float 0.0' => [
				'input'    => 0.0,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'negative float' => [
				'input'    => 5.600e-3,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'positive float' => [
				'input'    => 124.7,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'empty string' => [
				'input'    => '',
				'expected' => [
					'is_string_or_stringable' => true,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'string "123"' => [
				'input'    => '123',
				'expected' => [
					'is_string_or_stringable' => true,
					'is_numeric_array_key'    => true,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'string "foobar"' => [
				'input'    => 'foobar',
				'expected' => [
					'is_string_or_stringable' => true,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'string "123 My Street"' => [
				'input'    => '123 My Street',
				'expected' => [
					'is_string_or_stringable' => true,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'empty array' => [
				'input'    => [],
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => true,
					'is_iterable'             => true,
					'is_curl_handle'          => false,
				],
			],
			'array with three items, no keys' => [
				'input'    => [1, 2, 3],
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => true,
					'is_iterable'             => true,
					'is_curl_handle'          => false,
				],
			],
			'array with two items, with keys' => [
				'input'    => ['a' => 1, 'b' => 2],
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => true,
					'is_iterable'             => true,
					'is_curl_handle'          => false,
				],
			],
			'plain object' => [
				'input'    => new stdClass(),
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'object with __toString method' => [
				'input'    => new StringableObject('value'),
				'expected' => [
					'is_string_or_stringable' => true,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => true,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'object implementing ArrayIterator' => [
				'input'    => new ArrayIterator([1, 2, 3]),
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => true,
					'is_iterable'             => true,
					'is_curl_handle'          => false,
				],
			],
			'object implementing ArrayAccess' => [
				'input'    => new ArrayAccessibleObject(),
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => true,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
			'Curl handle (resource or object depending on PHP version)' => [
				'input'    => self::$curl_handle,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => true,
				],
			],
			'Directory handle (resource)' => [
				'input'    => self::$dir_handle,
				'expected' => [
					'is_string_or_stringable' => false,
					'is_numeric_array_key'    => false,
					'is_stringable_object'    => false,
					'has_array_access'        => false,
					'is_iterable'             => false,
					'is_curl_handle'          => false,
				],
			],
		];
	}
}
