<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Psr\HttpClient;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class CreateRequestTest extends TestCase {

	/**
	 * Tests receiving an Request when using createRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::createRequest
	 *
	 * @return void
	 */
	public function testCreateRequestWithUriInstanceReturnsRequest() {
		$http_client = new HttpClient([]);

		$uri = $this->createMock(UriInterface::class);

		$this->assertInstanceOf(
			RequestInterface::class,
			$http_client->createRequest('', $uri)
		);
	}

	/**
	 * Tests receiving an Request when using createRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::createRequest
	 *
	 * @return void
	 */
	public function testCreateRequestWithUriStringReturnsRequest() {
		$http_client = new HttpClient([]);

		$uri = 'https://example.org';

		$this->assertInstanceOf(
			RequestInterface::class,
			$http_client->createRequest('', $uri)
		);
	}

	/**
	 * Tests receiving an exception when the createRequest() method received an invalid input type as `$uri`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::createRequest
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testCreateRequestWithoutUriStringThrowsException($input) {
		$http_client = new HttpClient([]);

		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage(
			sprintf(
				'%s::createRequest(): Argument #2 ($uri) must be of type %s|string',
				HttpClient::class,
				UriInterface::class
			)
		);

		$http_client->createRequest('', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotString() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}
}
