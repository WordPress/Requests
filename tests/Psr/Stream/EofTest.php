<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class EofTest extends TestCase {

	/**
	 * Tests receiving true when using eof() method.
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::eof
	 *
	 * @return void
	 */
	public function testEofReturnsTrue() {
		$stream = Stream::createFromString('');

		$this->assertTrue($stream->eof());
	}
}
