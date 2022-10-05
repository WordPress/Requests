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
	public function testGetHeadersReturnsEmptyArray() {
		$response = Response::fromResponse(new RequestsResponse());

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
		$requestsResponse                  = new RequestsResponse();
		$requestsResponse->headers['name'] = 'value';

		$response = Response::fromResponse($requestsResponse);

		$this->assertSame(['name' => ['value']], $response->getHeaders());
	}
}
