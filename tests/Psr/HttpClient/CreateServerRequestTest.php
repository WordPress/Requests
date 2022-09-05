<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Exception;
use WpOrg\Requests\Psr\HttpClient;
use WpOrg\Requests\Tests\TestCase;

final class CreateServerRequestTest extends TestCase {

	/**
	 * Tests receiving an exception when using createServerRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::createServerRequest
	 *
	 * @return void
	 */
	public function testCreateServerRequest() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('not implemented');

		$httpClient = new HttpClient();
		$httpClient->createServerRequest('', '');
	}

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

		$request = $this->createMock('Psr\Http\Message\RequestInterface');

		$httpClient = new HttpClient();
		$httpClient->sendRequest($request);
	}
}
