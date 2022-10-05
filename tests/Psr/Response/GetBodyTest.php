<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use Psr\Http\Message\StreamInterface;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;

final class GetBodyTest extends TestCase {

	/**
	 * Tests receiving the stream when using getBody().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getBody
	 *
	 * @return void
	 */
	public function testGetBodyReturnsStreamInterface() {
		$requests_response              = new RequestsResponse();
		$requests_response->status_code = 200;
		$response                       = Response::fromResponse($requests_response);

		$this->assertInstanceOf(StreamInterface::class, $response->getBody());
	}

	/**
	 * Tests receiving the stream when using getBody().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getBody
	 *
	 * @return void
	 */
	public function testGetBodyReturnsStreamWithContent() {
		$requests_response              = new RequestsResponse();
		$requests_response->body        = 'response body';
		$requests_response->status_code = 200;
		$response                       = Response::fromResponse($requests_response);

		$this->assertSame('response body', $response->getBody()->__toString());
	}
}
