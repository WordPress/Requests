<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class CloseTest extends TestCase {

	/**
	 * Tests receiving void when using close() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::close
	 *
	 * @return void
	 */
	public function testCloseReturnsVoid() {
		$stream = StringBasedStream::createFromString('');

		$this->assertNull($stream->close());
	}
}
