<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class CloseTest extends TestCase {

	/**
	 * Tests receiving void when using close() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::close
	 *
	 * @return void
	 */
	public function testCloseReturnsVoid() {
		$stream = Stream::createFromString('');

		$this->assertNull($stream->close());
	}
}
