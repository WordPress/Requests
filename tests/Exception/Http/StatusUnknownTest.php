<?php

namespace WpOrg\Requests\Tests\Exception\Http;

use WpOrg\Requests\Exception\Http\StatusUnknown;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Exception\Http\StatusUnknown
 */
final class StatusUnknownTest extends TestCase {

	/**
	 * Test that the error code is set correctly.
	 *
	 * @dataProvider dataException
	 *
	 * @param int   $expected_code Expected error code.
	 * @param mixed $data          Optional. Input for the Exception constructor argument.
	 *
	 * @return void
	 */
	public function testException($expected_code, $data = null) {
		$this->expectException(StatusUnknown::class);
		$this->expectExceptionCode($expected_code);

		throw new StatusUnknown('testing-1-2-3', $data);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataException() {
		$response_with_status              = new Response();
		$response_with_status->status_code = 12345;

		$response_without_status = new Response();

		return [
			'null (or not passed)' => [
				'expectedCode' => 0,
			],
			'integer error code as data' => [
				'expectedCode' => 0,
				'data'         => 507,
			],
			'Response object with status code' => [
				'expectedCode' => 12345,
				'data'         => $response_with_status,
			],
			'Response object without status code' => [
				'expectedCode' => 0,
				'data'         => $response_without_status,
			],
		];
	}
}
