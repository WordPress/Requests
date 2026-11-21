<?php

namespace WpOrg\Requests\Tests\Response\Stream\Curl;

use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response\Stream\Curl;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response\Stream\Curl::__construct
 * @covers \WpOrg\Requests\Response\Stream\Curl::receive_body
 * @covers \WpOrg\Requests\Response\Stream\Curl::receive_trailer
 */
final class ReceiveBodyTest extends TestCase {

	public function testReceiveBodyBuffersData() {
		$stream = $this->makeStream();

		$this->assertSame(5, $stream->receive_body(null, 'hello'), 'All bytes should be accepted');
		$this->assertSame('hello', $stream->read(5), 'Buffered bytes should be served by read()');
	}

	public function testReceiveBodyRespectsMaxBytes() {
		$stream = $this->makeStream(8);

		$this->assertSame(8, $stream->receive_body(null, 'hello world'), 'Only the bytes within max_bytes should be accepted');
		$this->assertSame('hello wo', $stream->read(100));
		$this->assertTrue($stream->eof(), 'Stream should be at EOF once max_bytes is reached');
	}

	public function testReceiveTrailerDiscardsData() {
		$stream = $this->makeStream(false, 'abc');

		$this->assertSame(16, $stream->receive_trailer(null, "X-Trailer: yes\r\n"), 'Trailer length should be acknowledged');
		$this->assertSame('abc', $stream->read(100), 'Trailer data should not reach the body');
	}

	public function testPrebufferedBytesAreServedFirst() {
		$stream = $this->makeStream(false, 'early');

		$this->assertSame('early', $stream->read(5));
	}

	/**
	 * Build a stream around freshly initialised cURL handles.
	 *
	 * The transfer is never started, so tests can drive the callbacks directly.
	 *
	 * @param int|false $max_bytes   Maximum number of body bytes to read.
	 * @param string    $prebuffered Body bytes received before the handoff.
	 *
	 * @return \WpOrg\Requests\Response\Stream\Curl
	 */
	private function makeStream($max_bytes = false, $prebuffered = '') {
		$handle = curl_init('http://127.0.0.1:1/');
		$multi  = curl_multi_init();
		curl_multi_add_handle($multi, $handle);

		return new Curl($multi, $handle, $prebuffered, 1, $max_bytes, new Hooks());
	}
}
