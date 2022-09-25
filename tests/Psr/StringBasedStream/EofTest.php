<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class EofTest extends TestCase {

	/**
	 * Tests receiving true when using eof() method.
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::eof
	 *
	 * @return void
	 */
	public function testEofReturnsTrue() {
		$stream = StringBasedStream::createFromString('');

		$this->assertTrue($stream->eof());
	}
}
