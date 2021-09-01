<?php

namespace WpOrg\Requests\Tests\Exception;

use WpOrg\Requests\Exception\Http;
use WpOrg\Requests\Exception\Http\Status404;
use WpOrg\Requests\Exception\Http\StatusUnknown;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Exception\Http
 */
final class HttpTest extends TestCase {

	/**
	 * Test that the error message is set correctly.
	 *
	 * @dataProvider dataException
	 *
	 * @param string $expected_msg    Expected error message.
	 * @param string $expected_reason Expected error reason.
	 * @param mixed  $reason          Optional. Input for the $reason constructor argument.
	 *
	 * @return void
	 */
	public function testException($expected_msg, $expected_reason, $reason = null) {
		$this->expectException(Http::class);
		$this->expectExceptionMessage($expected_msg);

		$exception = new Http($reason);

		$this->assertSame($expected_reason, $exception->getReason());

		throw $exception;
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataException() {
		return array(
			'null (or not passed)' => array(
				'expected_msg'    => 'Unknown',
				'expected_reason' => 'Unknown',
			),
			'text string: "testing-1-2-3"' => array(
				'expected_msg'    => '0 testing-1-2-3',
				'expected_reason' => 'testing-1-2-3',
				'reason'          => 'testing-1-2-3',
			),
		);
	}

	/**
	 * Test whether the correct class is identified for a given status code.
	 *
	 * @dataProvider dataGetClass
	 *
	 * @param mixed  $code     HTTP status code or false if unavailable.
	 * @param string $expected The expected class name to be returned.
	 *
	 * @return void
	 */
	public function testGetClass($code, $expected) {
		$this->assertSame($expected, Http::get_class($code));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataGetClass() {
		$default = StatusUnknown::class;

		return array(
			'null' => array(
				'code'     => null,
				'expected' => $default,
			),
			'false' => array(
				'code'     => null,
				'expected' => $default,
			),
			'integer 0' => array(
				'code'     => 0,
				'expected' => $default,
			),
			'integer 404' => array(
				'code'     => 404,
				'expected' => '\\' . Status404::class,
			),
			'string 404' => array(
				'code'     => '404',
				'expected' => '\\' . Status404::class,
			),
			'integer 422: class does not exist' => array(
				'code'     => 422,
				'expected' => $default,
			),
		);
	}
}
