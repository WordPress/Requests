<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use RuntimeException;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class WriteTest extends TestCase {

	/**
	 * Tests receiving an exception when using write() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::write
	 *
	 * @return void
	 */
	public function testWriteThrowsRuntimeException() {
		$stream = Stream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::write() is not implemented.', Stream::class));

		$stream->write('');
	}
}
