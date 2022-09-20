<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use Psr\Http\Message\StreamInterface;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;

final class StreamTest extends TestCase {

	/**
	 * Tests all properties are set when using createFromString().
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::createFromString
	 *
	 * @return void
	 */
	public function testCreateFromStringReturnsStreamWillAllProperties() {
		$stream = Stream::createFromString('foobar');

		$this->assertSame(6, $stream->getSize());
		$this->assertSame('foobar', $stream->__toString());
	}
}
