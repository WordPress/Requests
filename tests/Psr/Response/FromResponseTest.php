<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use Psr\Http\Message\ResponseInterface;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;

final class FromResponseTest extends TestCase {

	/**
	 * Tests receiving a Response instance when using fromResponse().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::fromResponse
	 *
	 * @return void
	 */
	public function testFromResponseReturnsResponseInterface() {
		$requestsResponse = $this->createMock(RequestsResponse::class);

		$this->assertInstanceOf(
			ResponseInterface::class,
			Response::fromResponse($requestsResponse)
		);
	}
}
