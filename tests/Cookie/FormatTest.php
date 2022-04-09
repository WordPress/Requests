<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Tests\TestCase;

/**
 * @coversDefaultClass \WpOrg\Requests\Cookie
 */
final class FormatTest extends TestCase {

	/**
	 * Verify a Cookie is stringable.
	 *
	 * @covers ::__toString
	 *
	 * @return void
	 */
	public function testStringable() {
		$cookie = new Cookie('requests-testcookie', 'testvalue');

		$this->assertSame('testvalue', (string) $cookie);
	}

	/**
	 * Test formatting a cookie for a Cookie header.
	 *
	 * @covers ::format_for_header
	 *
	 * @dataProvider dataFormat
	 *
	 * @param string $name     Cookie name.
	 * @param string $value    Cookie value.
	 * @param array  $expected Expected function return values.
	 *
	 * @return void
	 */
	public function testFormatForHeader($name, $value, $expected) {
		$cookie = new Cookie($name, $value);

		$this->assertSame($expected, $cookie->format_for_header());
	}

	/**
	 * Test formatting a cookie for a Set-Cookie header.
	 *
	 * @covers ::format_for_set_cookie
	 *
	 * @dataProvider dataFormat
	 *
	 * @param string $name       Cookie name.
	 * @param string $value      Cookie value.
	 * @param array  $expected   Expected function return values.
	 *
	 * @return void
	 */
	public function testFormatForSetCookie($name, $value, $expected) {
		$cookie = new Cookie($name, $value);

		$this->assertSame($expected, $cookie->format_for_set_cookie());
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataFormat() {
		return [
			'Empty key, empty value' => [
				'name'     => '',
				'value'    => '',
				'expected' => '=',
			],
			'Empty key, has value' => [
				'name'     => '',
				'value'    => 'testvalue',
				'expected' => '=testvalue',
			],
			'Has key, empty value' => [
				'name'     => 'requests-testcookie',
				'value'    => '',
				'expected' => 'requests-testcookie=',
			],
			'Has key and value' => [
				'name'     => 'requests-testcookie',
				'value'    => 'testvalue',
				'expected' => 'requests-testcookie=testvalue',
			],
		];
	}

	/**
	 * Test formatting a cookie with attributes for a Set-Cookie header.
	 *
	 * @covers ::format_for_set_cookie
	 *
	 * @dataProvider dataFormatWithAttributes
	 *
	 * @param array  $attributes Cookie attributes.
	 * @param array  $expected   Expected function return values.
	 *
	 * @return void
	 */
	public function testFormatWithAttributes($attributes, $expected) {
		// Set the reference time to 2022-01-01 00:00:00.
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2022);

		$cookie = new Cookie('requests-testcookie', 'testvalue', $attributes, [], $reference_time);

		$this->assertSame($expected, $cookie->format_for_set_cookie());
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataFormatWithAttributes() {
		return [
			'Empty attributes array' => [
				'attributes' => [],
				'expected'   => 'requests-testcookie=testvalue',
			],
			'Single attribute with key' => [
				'attributes' => [
					'domain'  => 'example.org',
				],
				'expected'   => 'requests-testcookie=testvalue; domain=example.org',
			],
			'Single attribute without key' => [
				'attributes' => [
					'httponly',
				],
				'expected'   => 'requests-testcookie=testvalue; httponly',
			],
			'Attributes with and without key' => [
				'attributes' => [
					'domain'  => 'example.org',
					'httponly',
					'path'    => '/',
					'max-age' => '3600',
				],
				'expected'   => 'requests-testcookie=testvalue; domain=example.org; httponly; path=/; max-age=1640998800',
			],
		];
	}
}
