<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class DetachTest extends TestCase {

	/**
	 * Tests receiving null when using detach() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::detach
	 *
	 * @return void
	 */
	public function testDetachReturnsNull() {
		$stream = Stream::createFromString('');

		$this->assertNull($stream->detach());
	}
}
