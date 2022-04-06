<?php

namespace WpOrg\Requests\Tests\Exception\Transport\Curl;

use WpOrg\Requests\Exception\Transport\Curl;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Exception\Transport\Curl
 */
final class CurlTest extends TestCase {

	/**
	 * Test that the error message is set correctly.
	 *
	 * @dataProvider dataException
	 *
	 * @param string $expected_msg    Expected error message.
	 * @param int    $expected_code   Expected error code.
	 * @param string $expected_reason Expected error reason.
	 * @param string $message         Exception message.
	 * @param string $type            Exception type.
	 * @param mixed  $data            Optional. Associated data, if applicable.
	 * @param int    $code            Optional. Exception numerical code.
	 *
	 * @return void
	 */
	public function testException(
		$expected_msg,
		$expected_code,
		$expected_reason,
		$message,
		$type,
		$data = null,
		$code = 0
	) {
		$this->expectException(Curl::class);
		$this->expectExceptionMessage($expected_msg);
		$this->expectExceptionCode($expected_code);

		$exception = new Curl($message, $type, $data, $code);

		$this->assertSame($expected_reason, $exception->getReason());

		throw $exception;
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataException() {
		return [
			'Everything set to null (or not passed)' => [
				'expected_msg'    => '-1 Unknown',
				'expected_code'   => -1,
				'expected_reason' => 'Unknown',
				'message'         => null,
				'type'            => null,
				'data'            => null,
				'code'            => null,
			],
			'Message passed, everything else set to null (or not passed)' => [
				'expected_msg'    => '-1 testing-1-2-3',
				'expected_code'   => -1,
				'expected_reason' => 'testing-1-2-3',
				'message'         => 'testing-1-2-3',
				'type'            => null,
				'data'            => null,
				'code'            => null,
			],
			'Type passed, everything else set to null (or not passed)' => [
				'expected_msg'    => '-1 Unknown',
				'expected_code'   => -1,
				'expected_reason' => 'Unknown',
				'message'         => null,
				'type'            => Curl::EASY,
				'data'            => null,
				'code'            => null,
			],
			'Code passed, everything else set to null (or not passed)' => [
				'expected_msg'    => CURLE_COULDNT_RESOLVE_HOST . ' Unknown',
				'expected_code'   => CURLE_COULDNT_RESOLVE_HOST,
				'expected_reason' => 'Unknown',
				'message'         => null,
				'type'            => null,
				'data'            => null,
				'code'            => CURLE_COULDNT_RESOLVE_HOST,
			],
			'Message and type passed, everything else set to null (or not passed)' => [
				'expected_msg'    => '-1 testing-1-2-3',
				'expected_code'   => -1,
				'expected_reason' => 'testing-1-2-3',
				'message'         => 'testing-1-2-3',
				'type'            => Curl::EASY,
				'data'            => null,
				'code'            => null,
			],
			'Message and code passed, everything else set to null (or not passed)' => [
				'expected_msg'    => CURLE_COULDNT_RESOLVE_HOST . ' testing-1-2-3',
				'expected_code'   => CURLE_COULDNT_RESOLVE_HOST,
				'expected_reason' => 'testing-1-2-3',
				'message'         => 'testing-1-2-3',
				'type'            => null,
				'data'            => null,
				'code'            => CURLE_COULDNT_RESOLVE_HOST,
			],
			'Type and code passed, everything else set to null (or not passed)' => [
				'expected_msg'    => CURLE_COULDNT_RESOLVE_HOST . ' Unknown',
				'expected_code'   => CURLE_COULDNT_RESOLVE_HOST,
				'expected_reason' => 'Unknown',
				'message'         => null,
				'type'            => Curl::EASY,
				'data'            => null,
				'code'            => CURLE_COULDNT_RESOLVE_HOST,
			],
			'Everything set; code not integer' => [
				'expected_msg'    => '61 testing-1-2-3',
				'expected_code'   => 61,
				'expected_reason' => 'testing-1-2-3',
				'message'         => 'testing-1-2-3',
				'type'            => Curl::EASY,
				'data'            => [],
				'code'            => '61 text',
			],
		];
	}
}
