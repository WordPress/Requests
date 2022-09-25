<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;

final class WithBodyTest extends TestCase {

	/**
	 * Tests changing the body when using withBody().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withBody
	 *
	 * @return void
	 */
	public function testWithBodyReturnsResponse() {
		$requestsResponse = new RequestsResponse();
		$response = Response::fromResponse($requestsResponse);

		$this->assertInstanceOf(
			ResponseInterface::class,
			$response->withBody($this->createMock(StreamInterface::class))
		);
	}

	/**
	 * Tests changing the protocol version when using withBody().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withBody
	 *
	 * @return void
	 */
	public function testWithBodyReturnsNewInstance() {
		$requestsResponse = new RequestsResponse();
		$response = Response::fromResponse($requestsResponse);

		$this->assertNotSame(
			$response,
			$response->withBody($this->createMock(StreamInterface::class))
		);
	}
}
