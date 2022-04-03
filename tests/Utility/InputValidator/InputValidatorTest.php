<?php

namespace WpOrg\Requests\Tests\Utility\InputValidator;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
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
	 * Clean up after the tests.
	 */
	public static function tear_down_after_test() {
		if (isset(self::$curl_handle) && is_resource(self::$curl_handle)) {
			curl_close(self::$curl_handle);
		}

		parent::tear_down_after_test();
	}

	/**
	 * Test whether a received input parameter is correctly identified as usable as a valid numeric (integer) array key.
	 *
	 * @dataProvider dataIsNumericArrayKeyValid
	 *
	 * @covers ::is_numeric_array_key
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testIsNumericArrayKeyValid($input) {
		$this->assertTrue(InputValidator::is_numeric_array_key($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataIsNumericArrayKeyValid() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_INT, ['numeric string']);
	}

	/**
	 * Test whether a received input parameter is correctly identified as NOT valid as a numeric (integer) array key.
	 *
	 * @dataProvider dataIsNumericArrayKeyInvalid
	 *
	 * @covers ::is_numeric_array_key
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testIsNumericArrayKeyInvalid($input) {
		$this->assertFalse(InputValidator::is_numeric_array_key($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataIsNumericArrayKeyInvalid() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT, ['numeric string']);
	}

	/**
	 * Test whether a received input parameter is correctly identified as "stringable".
	 *
	 * @dataProvider dataIsStringableObjectValid
	 *
	 * @covers ::is_stringable_object
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testIsStringableObjectValid($input) {
		$this->assertTrue(InputValidator::is_stringable_object($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataIsStringableObjectValid() {
		return TypeProviderHelper::getSelection(['Stringable object']);
	}

	/**
	 * Test whether a received input parameter is correctly identified as NOT "stringable".
	 *
	 * @dataProvider dataIsStringableObjectInvalid
	 *
	 * @covers ::is_stringable_object
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testIsStringableObjectInvalid($input) {
		$this->assertFalse(InputValidator::is_stringable_object($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataIsStringableObjectInvalid() {
		return TypeProviderHelper::getAllExcept(['Stringable object']);
	}

	/**
	 * Test whether a received input parameter is correctly identified as "accessible as array".
	 *
	 * @dataProvider dataHasArrayAccessValid
	 *
	 * @covers ::has_array_access
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testHasArrayAccessValid($input) {
		$this->assertTrue(InputValidator::has_array_access($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataHasArrayAccessValid() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
	}

	/**
	 * Test whether a received input parameter is correctly identified as NOT "accessible as array".
	 *
	 * @dataProvider dataHasArrayAccessInvalid
	 *
	 * @covers ::has_array_access
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testHasArrayAccessInvalid($input) {
		$this->assertFalse(InputValidator::has_array_access($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataHasArrayAccessInvalid() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
	}

	/**
	 * Test whether a received input parameter is correctly identified as "iterable".
	 *
	 * @dataProvider dataIsIterableValid
	 *
	 * @covers ::is_iterable
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testIsIterableValid($input) {
		$this->assertTrue(InputValidator::is_iterable($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataIsIterableValid() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_ITERABLE);
	}

	/**
	 * Test whether a received input parameter is correctly identified as NOT "iterable".
	 *
	 * @dataProvider dataIsIterableInvalid
	 *
	 * @covers ::is_iterable
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testIsIterableInvalid($input) {
		$this->assertFalse(InputValidator::is_iterable($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataIsIterableInvalid() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ITERABLE);
	}

	/**
	 * Test whether a received input parameter is correctly identified as a Curl handle.
	 *
	 * @dataProvider dataIsCurlHandleValid
	 *
	 * @covers ::is_curl_handle
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testIsCurlHandleValid($input) {
		$this->assertTrue(InputValidator::is_curl_handle($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataIsCurlHandleValid() {
		if (isset(self::$curl_handle) === false) {
			self::$curl_handle = curl_init('http://httpbin.org/anything');
		}

		return [
			'Curl handle (resource or object depending on PHP version)' => [self::$curl_handle],
		];
	}

	/**
	 * Test whether a received input parameter is correctly identified as a Curl handle.
	 *
	 * @dataProvider dataIsCurlHandleInvalid
	 *
	 * @covers ::is_curl_handle
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testIsCurlHandleInvalid($input) {
		$this->assertFalse(InputValidator::is_curl_handle($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataIsCurlHandleInvalid() {
		return TypeProviderHelper::getAll();
	}
}
