<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WpOrg\Requests\Psr\HttpClient;
use WpOrg\Requests\Tests\TestCase;

final class SendRequestTest extends TestCase {

	/**
	 * Tests receiving an exception when using sendRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::sendRequest
	 *
	 * @return void
	 */
	public function testSendRequest() {
		$request = $this->createMock(RequestInterface::class);

		$httpClient = new HttpClient();

		$this->assertInstanceOf(
			ResponseInterface::class,
			$httpClient->sendRequest($request)
		);
	}
}
