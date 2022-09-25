<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class IsReadableTest extends TestCase {

	/**
	 * Tests receiving false when using isReadable() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::isReadable
	 *
	 * @return void
	 */
	public function testIsReadableReturnsFalse() {
		$stream = StringBasedStream::createFromString('');

		$this->assertFalse($stream->isReadable());
	}
}
