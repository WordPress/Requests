<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class IsSeekableTest extends TestCase {

	/**
	 * Tests receiving false when using isSeekable() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::isSeekable
	 *
	 * @return void
	 */
	public function testIsSeekableReturnsFalse() {
		$stream = Stream::createFromString('');

		$this->assertFalse($stream->isSeekable());
	}
}
