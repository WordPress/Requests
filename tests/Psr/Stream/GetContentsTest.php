<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use RuntimeException;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class GetContentsTest extends TestCase {

	/**
	 * Tests receiving an exception when using getContents() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::getContents
	 *
	 * @return void
	 */
	public function testGetContentsThrowsRuntimeException() {
		$stream = Stream::createFromString('');

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('%s::getContents() is not implemented.', Stream::class));

		$stream->getContents();
	}
}
