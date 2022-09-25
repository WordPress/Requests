<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use RuntimeException;
use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class SeekTest extends TestCase {

	/**
	 * Tests receiving an exception when using seek() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::seek
	 *
	 * @return void
	 */
	public function testSeekThrowsRuntimeException() {
		$stream = StringBasedStream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::seek() is not implemented.', StringBasedStream::class));

		$stream->seek(0);
	}
}
