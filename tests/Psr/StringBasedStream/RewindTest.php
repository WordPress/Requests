<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use RuntimeException;
use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class RewindTest extends TestCase {

	/**
	 * Tests receiving an exception when using rewind() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::rewind
	 *
	 * @return void
	 */
	public function testRewindThrowsRuntimeException() {
		$stream = StringBasedStream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::rewind() is not implemented.', StringBasedStream::class));

		$stream->rewind();
	}
}
