<?php

namespace WpOrg\Requests\Tests\Psr\StringBasedStream;

use WpOrg\Requests\Psr\StringBasedStream;
use WpOrg\Requests\Tests\TestCase;

final class StreamTest extends TestCase {

	/**
	 * Tests all properties are set when using createFromString().
	 *
	 * @covers \WpOrg\Requests\Psr\StringBasedStream::createFromString
	 *
	 * @return void
	 */
	public function testCreateFromStringReturnsStreamWithAllProperties() {
		$stream = StringBasedStream::createFromString('foobar');

		$this->assertSame(6, $stream->getSize());
		$this->assertSame('foobar', $stream->__toString());
	}
}
