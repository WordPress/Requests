<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Exception;
use Psr\Http\Message\RequestInterface;
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
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('not implemented');

		$request = $this->createMock(RequestInterface::class);

		$httpClient = new HttpClient();
		$httpClient->sendRequest($request);
	}
}
