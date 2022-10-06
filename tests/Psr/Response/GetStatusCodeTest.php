<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;

final class GetStatusCodeTest extends TestCase {

	/**
	 * Tests receiving the status code when using getStatusCode().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getStatusCode
	 *
	 * @return void
	 */
	public function testGetStatusCodeReturnsInteger() {
		$requests_response              = new RequestsResponse();
		$requests_response->status_code = 200;
		$response                       = Response::fromResponse($requests_response);

		$this->assertSame(200, $response->getStatusCode());
	}
}