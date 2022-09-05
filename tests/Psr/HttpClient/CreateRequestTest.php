<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Exception;
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
	public function testCreateRequest() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('not implemented');

		$httpClient = new HttpClient();
		$httpClient->createRequest('', '');
	}
}
