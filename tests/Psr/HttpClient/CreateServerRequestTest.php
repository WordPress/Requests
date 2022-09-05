<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Exception;
use WpOrg\Requests\Psr\HttpClient;
use WpOrg\Requests\Tests\TestCase;

final class CreateServerRequestTest extends TestCase {

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$url`.
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
}
