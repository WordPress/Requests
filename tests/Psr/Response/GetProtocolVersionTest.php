<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;

final class GetProtocolVersionTest extends TestCase {

	/**
	 * Tests receiving the protocol version when using getProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getProtocolVersion
	 *
	 * @return void
	 */
	public function testGetProtocolVersionWithFloatReturnsString() {
		$requests_response                   = new RequestsResponse();
		$requests_response->status_code      = 200;
		$requests_response->protocol_version = 1.0;
		$response                            = Response::fromResponse($requests_response);

		$this->assertSame('1.0', $response->getProtocolVersion());
	}

	/**
	 * Tests receiving the protocol version when using getProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getProtocolVersion
	 *
	 * @return void
	 */
	public function testGetProtocolVersionWithFalseReturnsString() {
		$requests_response                   = new RequestsResponse();
		$requests_response->status_code      = 200;
		$requests_response->protocol_version = false;
		$response                            = Response::fromResponse($requests_response);

		$this->assertSame('1.1', $response->getProtocolVersion());
	}
}
