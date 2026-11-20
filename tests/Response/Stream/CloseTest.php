<?php

namespace WpOrg\Requests\Tests\Response\Stream;

use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response\Stream\Socket;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response\Stream::close
 * @covers \WpOrg\Requests\Response\Stream::__destruct
 * @covers \WpOrg\Requests\Response\Stream\Socket::free
 */
final class CloseTest extends TestCase {

	public function testCloseStopsReads() {
		$stream = $this->streamFromString('abcdef');

		$this->assertSame('abc', $stream->read(3), 'Pre-condition failed: first read should succeed');

		$stream->close();

		$this->assertTrue($stream->eof(), 'Stream should be at EOF after close()');
		$this->assertSame('', $stream->read(3), 'Reading a closed stream should return an empty string');
	}

	public function testCloseReleasesTheSocket() {
		$handle = fopen('php://temp', 'r+b');
		$stream = new Socket($handle, false, false, new Hooks());

		$stream->close();

		$this->assertFalse(is_resource($handle), 'Underlying socket should be closed');
	}

	public function testCloseIsIdempotent() {
		$stream = $this->streamFromString('abc');

		$stream->close();
		$stream->close();

		$this->assertTrue($stream->eof());
	}

	public function testDestructorReleasesTheSocket() {
		$handle = fopen('php://temp', 'r+b');
		$stream = new Socket($handle, false, false, new Hooks());

		unset($stream);

		$this->assertFalse(is_resource($handle), 'Underlying socket should be closed on destruct');
	}

	/**
	 * Build a socket-backed stream serving a fixed string from memory.
	 *
	 * @param string $data Raw body bytes.
	 *
	 * @return \WpOrg\Requests\Response\Stream\Socket
	 */
	private function streamFromString($data) {
		$handle = fopen('php://temp', 'r+b');
		fwrite($handle, $data);
		rewind($handle);

		return new Socket($handle, false, false, new Hooks());
	}
}
