<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response;
use WpOrg\Requests\Response\Stream\Socket;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response::close
 */
final class CloseTest extends TestCase {

	public function testCloseWithoutStreamIsNoop() {
		$response = new Response();
		$response->close();

		$this->assertFalse($response->is_streaming());
	}

	public function testCloseClosesAttachedStream() {
		$handle = fopen('php://temp', 'r+b');
		fwrite($handle, 'abcdef');
		rewind($handle);

		$response = new Response();
		$response->set_stream(new Socket($handle, false, false, new Hooks()));

		$response->close();

		$this->assertTrue($response->eof(), 'Stream should be at EOF after close()');
		$this->assertFalse(is_resource($handle), 'Underlying socket should be closed');
		$this->assertSame('', $response->read(), 'Reading a closed stream should return an empty string');
	}
}
