<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class IsReadableTest extends TestCase {

	/**
	 * Tests receiving false when using isReadable() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::isReadable
	 *
	 * @return void
	 */
	public function testIsReadableReturnsFalse() {
		$stream = Stream::createFromString('');

		$this->assertFalse($stream->isReadable());
	}
}
