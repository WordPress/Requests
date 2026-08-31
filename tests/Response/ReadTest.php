<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response;
use WpOrg\Requests\Response\Stream\Socket;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response::read
 */
final class ReadTest extends TestCase {

	public function testReadThrowsWhenNotStreaming() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Response is not being streamed');

		$response = new Response();
		$response->read();
	}

	public function testReadReadsFromAttachedStream() {
		$response = $this->streamingResponse('abcdef');

		$this->assertSame('abc', $response->read(3));
		$this->assertSame('def', $response->read(3));
	}

	public function testReadLeavesBodyUntouched() {
		$response = $this->streamingResponse('abcdef');
		$response->read();

		$this->assertSame('', $response->body);
	}

	/**
	 * Build a response with a socket-backed body stream attached.
	 *
	 * @param string $data Raw body bytes.
	 *
	 * @return \WpOrg\Requests\Response
	 */
	private function streamingResponse($data) {
		$handle = fopen('php://temp', 'r+b');
		fwrite($handle, $data);
		rewind($handle);

		$response = new Response();
		$response->set_stream(new Socket($handle, false, false, new Hooks()));

		return $response;
	}
}
