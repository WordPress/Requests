<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use RuntimeException;
use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class WriteTest extends TestCase {

	/**
	 * Tests receiving an exception when using write() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::write
	 *
	 * @return void
	 */
	public function testWriteThrowsRuntimeException() {
		$stream = StringBasedStream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::write() is not implemented.', StringBasedStream::class));

		$stream->write('');
	}
}
