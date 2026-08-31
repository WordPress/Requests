<?php

namespace WpOrg\Requests\Tests\Response\Stream\Curl;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response\Stream\Curl;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response\Stream\Curl::fetch
 */
final class FetchTest extends TestCase {

	public function testReadThrowsTimeoutWhenNoDataArrives() {
		// A listener which accepts the connection but never responds.
		$server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
		$stream = $this->makeStream('http://' . stream_socket_get_name($server, false) . '/');

		try {
			$this->expectException(Exception::class);
			$this->expectExceptionMessage('Stream timed out while waiting for body data');

			$stream->read();
		} finally {
			$stream->close();
			fclose($server);
		}
	}

	public function testReadThrowsCurlerrorWhenTheTransferFails() {
		// Grab a free port, then close the listener so connecting to it fails.
		$server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
		$url    = 'http://' . stream_socket_get_name($server, false) . '/';
		fclose($server);

		$stream = $this->makeStream($url);

		try {
			$this->expectException(Exception::class);
			$this->expectExceptionMessage('cURL error');

			$stream->read();
		} finally {
			$stream->close();
		}
	}

	/**
	 * Build a stream driving a not-yet-started transfer to the given URL.
	 *
	 * @param string $url URL to request.
	 *
	 * @return \WpOrg\Requests\Response\Stream\Curl
	 */
	private function makeStream($url) {
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
		$multi = curl_multi_init();
		curl_multi_add_handle($multi, $handle);

		return new Curl($multi, $handle, '', 1, false, new Hooks());
	}
}
