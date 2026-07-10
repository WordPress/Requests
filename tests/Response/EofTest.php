<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response;
use WpOrg\Requests\Response\Stream\Socket;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response::eof
 */
final class EofTest extends TestCase {

	public function testEofThrowsWhenNotStreaming() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Response is not being streamed');

		$response = new Response();
		$response->eof();
	}

	public function testEofReflectsStreamState() {
		$handle = fopen('php://temp', 'r+b');
		fwrite($handle, 'abc');
		rewind($handle);

		$response = new Response();
		$response->set_stream(new Socket($handle, false, false, new Hooks()));

		$this->assertFalse($response->eof(), 'EOF should not be reached before reading');

		while (!$response->eof()) {
			$response->read();
		}

		$this->assertTrue($response->eof(), 'EOF should be reached once the body is drained');
	}
}
