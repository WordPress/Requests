<?php

namespace WpOrg\Requests\Tests\Utility\InputValidator;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\InputValidator;

/**
 * @covers \WpOrg\Requests\Utility\InputValidator::is_curl_handle
 */
final class IsCurlHandleTest extends TestCase {

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
	 * Test whether a received input parameter is correctly identified as a Curl handle.
	 *
	 * @dataProvider dataValid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testValid($input) {
		$this->assertTrue(InputValidator::is_curl_handle($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataValid() {
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
	 * @dataProvider dataInvalid
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testInvalid($input) {
		$this->assertFalse(InputValidator::is_curl_handle($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalid() {
		return TypeProviderHelper::getAll();
	}
}
