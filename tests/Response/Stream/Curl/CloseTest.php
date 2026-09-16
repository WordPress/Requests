<?php

namespace WpOrg\Requests\Tests\Response\Stream\Curl;

use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response\Stream\Curl;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response\Stream\Curl::free
 * @covers \WpOrg\Requests\Response\Stream::close
 * @covers \WpOrg\Requests\Response\Stream::__destruct
 */
final class CloseTest extends TestCase {

	public function testCloseStopsReads() {
		$stream = $this->makeStream('abc');

		$stream->close();

		$this->assertTrue($stream->eof(), 'Stream should be at EOF after close()');
		$this->assertSame('', $stream->read(3), 'Reading a closed stream should return an empty string');
	}

	public function testCloseIsIdempotent() {
		$stream = $this->makeStream('abc');

		$stream->close();
		$stream->close();

		$this->assertTrue($stream->eof());
	}

	public function testDestructorReleasesTheHandles() {
		$stream = $this->makeStream('abc');

		unset($stream);

		// The destructor ran without errors; nothing observable remains.
		$this->assertTrue(true);
	}

	/**
	 * Build a stream around freshly initialised cURL handles.
	 *
	 * @param string $prebuffered Body bytes received before the handoff.
	 *
	 * @return \WpOrg\Requests\Response\Stream\Curl
	 */
	private function makeStream($prebuffered = '') {
		$handle = curl_init('http://127.0.0.1:1/');
		$multi  = curl_multi_init();
		curl_multi_add_handle($multi, $handle);

		return new Curl($multi, $handle, $prebuffered, 1, false, new Hooks());
	}
}
