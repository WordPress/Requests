<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class IsWritableTest extends TestCase {

	/**
	 * Tests receiving false when using isWritable() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::isWritable
	 *
	 * @return void
	 */
	public function testIsWritableReturnsFalse() {
		$stream = Stream::createFromString('');

		$this->assertFalse($stream->isWritable());
	}
}
