<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class DetachTest extends TestCase {

	/**
	 * Tests receiving null when using detach() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::detach
	 *
	 * @return void
	 */
	public function testDetachReturnsNull() {
		$stream = StringBasedStream::createFromString('');

		$this->assertNull($stream->detach());
	}
}
