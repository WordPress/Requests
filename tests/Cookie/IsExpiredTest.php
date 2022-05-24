<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Cookie::is_expired
 */
final class IsExpiredTest extends TestCase {

	/**
	 * Test correctly identifying whether or not a cookie is expired.
	 *
	 * @dataProvider dataCookieExpiration
	 *
	 * @param array $attributes The attributes to use for creating the cookie.
	 * @param bool  $expected   Expected function return value.
	 *
	 * @return void
	 */
	public function testCookieExpiration($attributes, $expected) {
		// Set the reference time to 2022-01-01 00:00:00.
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2022);

		$cookie = new Cookie('requests-testcookie', 'testvalue', $attributes, [], $reference_time);

		$this->assertSame($expected, $cookie->is_expired());
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataCookieExpiration() {
		return [
			'Empty attributes array' => [
				'attributes' => [],
				'expected'   => false,
			],
			'Non-empty attributes array, but no max-age or expires keys' => [
				'attributes' => [
					'domain' => 'example.org',
					'path'   => '/',
				],
				'expected'   => false,
			],
			'Max-age key set, cookie not expired' => [
				'attributes' => [
					'max-age' => gmmktime(1, 1, 0, 1, 1, 2022),
				],
				'expected'   => false,
			],
			'Max-age key set, max-age === reference time, cookie not expired' => [
				'attributes' => [
					'max-age' => gmmktime(0, 0, 0, 1, 1, 2022),
				],
				'expected'   => false,
			],
			'Max-age key set, cookie expired' => [
				'attributes' => [
					'max-age' => gmmktime(0, 0, 0, 12, 1, 2021),
				],
				'expected'   => true,
			],
			'Expires key set, cookie not expired' => [
				'attributes' => [
					'expires' => gmmktime(0, 0, 0, 2, 1, 2022),
				],
				'expected'   => false,
			],
			'Expires key set, expires === reference time, cookie not expired' => [
				'attributes' => [
					'expires' => gmmktime(0, 0, 0, 1, 1, 2022),
				],
				'expected'   => false,
			],
			'Expires key set, cookie expired' => [
				'attributes' => [
					'expires' => gmmktime(0, 0, 0, 12, 1, 2021),
				],
				'expected'   => true,
			],
			'Both max-age and expires keys set, max-age takes precedence, cookie not expired' => [
				'attributes' => [
					'max-age'       => gmmktime(0, 0, 0, 2, 1, 2022),
					'expires'       => gmmktime(0, 0, 0, 12, 1, 2021),
				],
				'expected'   => false,
			],
			'Both max-age and expires keys set, max-age takes precedence, cookie expired' => [
				'attributes' => [
					'max-age'       => gmmktime(0, 0, 0, 12, 1, 2021),
					'expires'       => gmmktime(0, 0, 0, 2, 1, 2022),
				],
				'expected'   => true,
			],
		];
	}
}
