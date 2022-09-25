<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class IsSeekableTest extends TestCase {

	/**
	 * Tests receiving false when using isSeekable() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::isSeekable
	 *
	 * @return void
	 */
	public function testIsSeekableReturnsFalse() {
		$stream = StringBasedStream::createFromString('');

		$this->assertFalse($stream->isSeekable());
	}
}
