<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use RuntimeException;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class SeekTest extends TestCase {

	/**
	 * Tests receiving an exception when using seek() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::seek
	 *
	 * @return void
	 */
	public function testSeekThrowsRuntimeException() {
		$stream = Stream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::seek() is not implemented.', Stream::class));

		$stream->seek(0);
	}
}
