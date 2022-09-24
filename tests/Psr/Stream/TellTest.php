<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use RuntimeException;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class TellTest extends TestCase {

	/**
	 * Tests receiving an exception when using tell() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::tell
	 *
	 * @return void
	 */
	public function testTellThrowsRuntimeException() {
		$stream = Stream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::tell() is not implemented.', Stream::class));

		$stream->tell();
	}
}
