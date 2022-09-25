<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;

final class GetHeadersTest extends TestCase {

	/**
	 * Tests receiving the headers when using getHeaders().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeaders
	 *
	 * @return void
	 */
	public function _testGetHeadersReturnsEmptyArray() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertSame([], $response->getHeaders());
	}

	/**
	 * Tests receiving the headers when using getHeaders().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeaders
	 *
	 * @return void
	 */
	public function testGetHeadersReturnsArray() {
		$requestsResponse = new RequestsResponse();
		$requestsResponse->headers['name'] = 'value';
		var_dump($requestsResponse->headers);
		$response = Response::fromResponse($requestsResponse);

		$this->assertSame(['name' => ['value']], $response->getHeaders());
	}
}
