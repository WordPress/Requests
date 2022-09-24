<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use RuntimeException;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class RewindTest extends TestCase {

	/**
	 * Tests receiving an exception when using rewind() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::rewind
	 *
	 * @return void
	 */
	public function testRewindThrowsRuntimeException() {
		$stream = Stream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::rewind() is not implemented.', Stream::class));

		$stream->rewind();
	}
}
