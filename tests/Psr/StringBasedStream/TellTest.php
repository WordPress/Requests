<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use RuntimeException;
use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class TellTest extends TestCase {

	/**
	 * Tests receiving an exception when using tell() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::tell
	 *
	 * @return void
	 */
	public function testTellThrowsRuntimeException() {
		$stream = StringBasedStream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::tell() is not implemented.', StringBasedStream::class));

		$stream->tell();
	}
}
