<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use RuntimeException;
use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class ReadTest extends TestCase {

	/**
	 * Tests receiving an exception when using read() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::read
	 *
	 * @return void
	 */
	public function testReadThrowsRuntimeException() {
		$stream = StringBasedStream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::read() is not implemented.', StringBasedStream::class));

		$stream->read(0);
	}
}
