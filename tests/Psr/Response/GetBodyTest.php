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
		$requestsResponse = new RequestsResponse();
		$requestsResponse->status_code = 200;
		$response = Response::fromResponse($requestsResponse);

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
		$requestsResponse = new RequestsResponse();
		$requestsResponse->body = 'response body';
		$requestsResponse->status_code = 200;
		$response = Response::fromResponse($requestsResponse);

		$this->assertSame('response body', $response->getBody()->__toString());
	}
}
