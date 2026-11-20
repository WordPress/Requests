<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response;
use WpOrg\Requests\Response\Stream\Socket;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response::is_streaming
 * @covers \WpOrg\Requests\Response::set_stream
 */
final class IsStreamingTest extends TestCase {

	public function testNotStreamingByDefault() {
		$response = new Response();

		$this->assertFalse($response->is_streaming());
	}

	public function testStreamingOnceStreamAttached() {
		$handle = fopen('php://temp', 'r+b');

		$response = new Response();
		$response->set_stream(new Socket($handle, false, false, new Hooks()));

		$this->assertTrue($response->is_streaming());
	}
}
