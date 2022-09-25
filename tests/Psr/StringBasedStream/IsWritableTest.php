<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class IsWritableTest extends TestCase {

	/**
	 * Tests receiving false when using isWritable() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::isWritable
	 *
	 * @return void
	 */
	public function testIsWritableReturnsFalse() {
		$stream = StringBasedStream::createFromString('');

		$this->assertFalse($stream->isWritable());
	}
}
