<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use RuntimeException;
use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class GetContentsTest extends TestCase {

	/**
	 * Tests receiving an exception when using getContents() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::getContents
	 *
	 * @return void
	 */
	public function testGetContentsThrowsRuntimeException() {
		$stream = StringBasedStream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::getContents() is not implemented.', StringBasedStream::class));

		$stream->getContents();
	}
}
