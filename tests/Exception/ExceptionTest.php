<?php

namespace WpOrg\Requests\Tests\Exception;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Exception
 */
final class ExceptionTest extends TestCase {

	/**
	 * Test that the exception is created correctly.
	 *
	 * @return void
	 */
	public function testConstructor() {
		$message = 'Message';
		$type    = 'requests.code';
		$data    = ['data'];
		$code    = 103;

		$exception = new Exception($message, $type, $data, $code);

		$this->assertSame($message, $exception->getMessage(), 'Message was not set correctly');
		$this->assertSame($type, $exception->getType(), 'Type was not set correctly');
		$this->assertSame($data, $exception->getData(), 'Data was not set correctly');
		$this->assertSame($code, $exception->getCode(), 'Code was not set correctly');
	}

	/**
	 * Test that the exception gets thrown correctly.
	 *
	 * @return void
	 */
	public function testException() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Message');

		throw new Exception('Message', 'Type');
	}
}
