<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use RuntimeException;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class ReadTest extends TestCase {

	/**
	 * Tests receiving an exception when using read() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::read
	 *
	 * @return void
	 */
	public function testReadThrowsRuntimeException() {
		$stream = Stream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::read() is not implemented.', Stream::class));

		$stream->read(0);
	}
}
