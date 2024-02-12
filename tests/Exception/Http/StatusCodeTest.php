<?php

namespace WpOrg\Requests\Tests\Exception\Http;

use WpOrg\Requests\Exception\Http;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\HttpStatus;

/**
 * @covers \WpOrg\Requests\Exception\Http\Status304
 * @covers \WpOrg\Requests\Exception\Http\Status305
 * @covers \WpOrg\Requests\Exception\Http\Status306
 * @covers \WpOrg\Requests\Exception\Http\Status400
 * @covers \WpOrg\Requests\Exception\Http\Status401
 * @covers \WpOrg\Requests\Exception\Http\Status402
 * @covers \WpOrg\Requests\Exception\Http\Status403
 * @covers \WpOrg\Requests\Exception\Http\Status404
 * @covers \WpOrg\Requests\Exception\Http\Status405
 * @covers \WpOrg\Requests\Exception\Http\Status406
 * @covers \WpOrg\Requests\Exception\Http\Status407
 * @covers \WpOrg\Requests\Exception\Http\Status408
 * @covers \WpOrg\Requests\Exception\Http\Status409
 * @covers \WpOrg\Requests\Exception\Http\Status410
 * @covers \WpOrg\Requests\Exception\Http\Status411
 * @covers \WpOrg\Requests\Exception\Http\Status412
 * @covers \WpOrg\Requests\Exception\Http\Status413
 * @covers \WpOrg\Requests\Exception\Http\Status414
 * @covers \WpOrg\Requests\Exception\Http\Status415
 * @covers \WpOrg\Requests\Exception\Http\Status416
 * @covers \WpOrg\Requests\Exception\Http\Status417
 * @covers \WpOrg\Requests\Exception\Http\Status418
 * @covers \WpOrg\Requests\Exception\Http\Status421
 * @covers \WpOrg\Requests\Exception\Http\Status422
 * @covers \WpOrg\Requests\Exception\Http\Status423
 * @covers \WpOrg\Requests\Exception\Http\Status424
 * @covers \WpOrg\Requests\Exception\Http\Status425
 * @covers \WpOrg\Requests\Exception\Http\Status426
 * @covers \WpOrg\Requests\Exception\Http\Status428
 * @covers \WpOrg\Requests\Exception\Http\Status429
 * @covers \WpOrg\Requests\Exception\Http\Status431
 * @covers \WpOrg\Requests\Exception\Http\Status451
 * @covers \WpOrg\Requests\Exception\Http\Status500
 * @covers \WpOrg\Requests\Exception\Http\Status501
 * @covers \WpOrg\Requests\Exception\Http\Status502
 * @covers \WpOrg\Requests\Exception\Http\Status503
 * @covers \WpOrg\Requests\Exception\Http\Status504
 * @covers \WpOrg\Requests\Exception\Http\Status505
 * @covers \WpOrg\Requests\Exception\Http\Status506
 * @covers \WpOrg\Requests\Exception\Http\Status507
 * @covers \WpOrg\Requests\Exception\Http\Status508
 * @covers \WpOrg\Requests\Exception\Http\Status510
 * @covers \WpOrg\Requests\Exception\Http\Status511
 * @covers \WpOrg\Requests\Exception\Http\StatusUnknown
 */
final class StatusCodeTest extends TestCase {

	/**
	 * Test whether all HTTP exception classes can be instantiated for unknown status codes.
	 *
	 * We're making sure that if such an exception gets triggered, it will not
	 * fatal because of a broken exception class.
	 *
	 * @dataProvider dataUnknownStatusCodes
	 *
	 * @param int   $expected_code Expected error code.
	 * @param mixed $data          Optional. Input for the Exception constructor argument.
	 *
	 * @return void
	 */
	public function testUnknownStatusCodes($expected_code, $data = null) {
		$this->expectException(Http\StatusUnknown::class);
		$this->expectExceptionCode($expected_code);

		throw new Http\StatusUnknown('testing-1-2-3', $data);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataUnknownStatusCodes() {
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

	/**
	 * Test whether all HTTP exception classes can be instantiated for known status codes.
	 *
	 * We're making sure that if such an exception gets triggered, it will not
	 * fatal because of a broken exception class.
	 *
	 * @dataProvider dataKnownStatusCodes
	 *
	 * @param int    status_code               HTTP status code.
	 * @param string $expected_exception_class Exception class to expect.
	 *
	 * @return void
	 */
	public function testKnownStatusCodes($status_code, $expected_exception_class) {
		$response_with_status              = new Response();
		$response_with_status->status_code = $status_code;

		$exception_class  = Http::get_class($status_code);
		$exception_object = new $exception_class('testing-1-2-3');

		$this->assertInstanceOf(Http::class, $exception_object);
		$this->assertInstanceOf($expected_exception_class, $exception_object);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataKnownStatusCodes() {
		$all_status_codes    = array_keys(HttpStatus::MAP);
		$non_exception_codes = [100, 101, 102, 103, 200, 201, 202, 203, 204, 205, 206, 207, 208, 226, 300, 301, 302, 303, 307, 308];

		$data = [];
		foreach ($all_status_codes as $status) {
			$classname = in_array($status, $non_exception_codes, true)
				? Http\StatusUnknown::class
				: Http::class . '\Status' . $status;

			$data['Status ' . $status] = [$status, $classname];
		}

		return $data;
	}
}
