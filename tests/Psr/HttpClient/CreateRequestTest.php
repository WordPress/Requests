<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Psr\Http\Message\RequestInterface;
use WpOrg\Requests\Psr\HttpClient;
use WpOrg\Requests\Tests\TestCase;

final class CreateRequestTest extends TestCase {

	/**
	 * Tests receiving an exception when using createRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::createRequest
	 *
	 * @return void
	 */
	public function testCreateRequestReturnsRequest() {
		$httpClient = new HttpClient();

		$this->assertInstanceOf(
			RequestInterface::class,
			$httpClient->createRequest('', '')
		);
	}
}
