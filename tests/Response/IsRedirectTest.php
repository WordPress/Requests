<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Response::is_redirect
 */
final class IsRedirectTest extends TestCase {

	/**
	 * Verify a redirection status code is identified correctly.
	 *
	 * @dataProvider dataIsRedirect
	 *
	 * @param int $code Status code.
	 *
	 * @return void
	 */
	public function testIsRedirect($code) {
		$response              = new Response();
		$response->status_code = $code;

		$this->assertTrue($response->is_redirect());
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataIsRedirect() {
		$data = [];

		$codes = [300, 301, 302, 303];
		foreach ($codes as $code) {
			$data['Status code: ' . $code] = [$code];
		}

		$codes = range(307, 399, 1);
		foreach ($codes as $code) {
			$data['Status code: ' . $code] = [$code];
		}

		return $data;
	}

	/**
	 * Verify a non-redirection status code is identified correctly.
	 *
	 * @dataProvider dataNotRedirect
	 *
	 * @param int $code Status code.
	 *
	 * @return void
	 */
	public function testNotRedirect($code) {
		$response              = new Response();
		$response->status_code = $code;

		$this->assertFalse($response->is_redirect());
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataNotRedirect() {
		$data = [];

		$data['Non-blocking request: status code: false (default value)'] = [false];

		$codes = range(100, 299, 1);
		foreach ($codes as $code) {
			$data['Status code: ' . $code] = [$code];
		}

		$codes = [304, 305, 306];
		foreach ($codes as $code) {
			$data['Status code: ' . $code] = [$code];
		}

		$codes = range(400, 599, 1);
		foreach ($codes as $code) {
			$data['Status code: ' . $code] = [$code];
		}

		$except   = TypeProviderHelper::GROUP_INT;
		$except[] = 'boolean false'; // No need to double test `false`.
		$invalid  = TypeProviderHelper::getAllExcept($except);
		foreach ($invalid as $key => $type) {
			$data['Invalid status code: ' . $key] = [$type['input']];
		}

		return $data;
	}
}
