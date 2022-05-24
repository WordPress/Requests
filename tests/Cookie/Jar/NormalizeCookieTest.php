<?php

namespace WpOrg\Requests\Tests\Cookie\Jar;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Cookie\Jar::normalize_cookie
 */
final class NormalizeCookieTest extends TestCase {

	const COOKIE_VALUE = 'my-value';

	/**
	 * Verify that cookie normalization works on both prebaked and unbaked cookies when not passing a key.
	 *
	 * @dataProvider dataNormalization
	 *
	 * @param mixed $cookie Cookie header value, possibly pre-parsed (object).
	 *
	 * @return void
	 */
	public function testNormalizationWithoutKey($cookie) {
		$jar    = new Jar();
		$result = $jar->normalize_cookie($cookie);

		$this->assertInstanceOf(Cookie::class, $result, 'Normalized cookie is not an instance of Cookie class');
		$this->assertSame(self::COOKIE_VALUE, (string) $result, 'Cookie value is not the expected value');
	}

	/**
	 * Verify that cookie normalization works on both prebaked and unbaked cookies when passing a key.
	 *
	 * @dataProvider dataNormalization
	 *
	 * @param mixed $cookie Cookie header value, possibly pre-parsed (object).
	 *
	 * @return void
	 */
	public function testNormalizationWithKey($cookie, $expected_name) {
		$jar    = new Jar();
		$result = $jar->normalize_cookie($cookie, 'different-name');

		$this->assertInstanceOf(Cookie::class, $result, 'Normalized cookie is not an instance of Cookie class');
		$this->assertSame(self::COOKIE_VALUE, (string) $result, 'Cookie value is not the expected value');

		// This also verifies that if the cookie is pre-baked, passing a name will not overwrite the original cookie name.
		$this->assertSame($expected_name, $result->name, 'Cookie name is not the expected name');
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataNormalization() {
		return [
			'unbaked cookie (string)' => [
				'cookie'        => self::COOKIE_VALUE,
				'expected_name' => 'different-name',
			],
			'pre-baked cookie (object)' => [
				'cookie'        => new Cookie('my-cookie', self::COOKIE_VALUE),
				'expected_name' => 'my-cookie',
			],
		];
	}
}
