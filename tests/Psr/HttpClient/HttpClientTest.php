<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Psr\Http\Message\ResponseInterface;
use WpOrg\Requests\Exception\Psr\ClientException;
use WpOrg\Requests\Exception\Psr\NetworkException;
use WpOrg\Requests\Exception\Psr\RequestException;
use WpOrg\Requests\Exception\Transport as ExceptionTransport;
use WpOrg\Requests\Psr\HttpClient;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Transport;

final class HttpClientTest extends TestCase {

	/**
	 * Tests receiving a response when using sendRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::sendRequest
	 *
	 * @return void
	 */
	public function testSendRequestSendsCorrectDataAndReturnsCorrectResponseData() {
		$transport = $this->createMock(Transport::class);
		$transport->expects($this->once())->method('request')->willReturnCallback(
			function ($url, $headers, $data, $options) use ($transport) {
				$this->assertSame('https://example.org/', $url);
				$this->assertSame(['Host' => 'example.org'], $headers);
				$this->assertSame('', $data);
				$this->assertSame('GET', $options['type']);

				return 'HTTP/1.1 200 OK' . "\r\n" .
				'Content-Type:text/plain' . "\r\n" .
				"\r\n" .
				'foobar';
			}
		);

		$http_client = new HttpClient(
			[
				'transport' => $transport,
			]
		);

		$request = $http_client->createRequest('GET', 'https://example.org');

		$response = $http_client->sendRequest($request);

		$this->assertInstanceOf(ResponseInterface::class, $response);
		$this->assertSame(200, $response->getStatusCode());
		$this->assertSame('OK', $response->getReasonPhrase());
		$this->assertSame('1.1', $response->getProtocolVersion());
		$this->assertSame(['content-type' => ['text/plain']], $response->getHeaders());
		$this->assertSame('foobar', $response->getBody()->__toString());
	}

	/**
	 * Tests receiving a response when using sendRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::sendRequest
	 *
	 * @return void
	 */
	public function testSendRequestReturnsResponseOn404Error() {
		$transport = $this->createMock(Transport::class);
		$transport->expects($this->once())->method('request')->willReturnCallback(
			function ($url, $headers, $data, $options) use ($transport) {
				$this->assertSame('https://example.org/not-found', $url);
				$this->assertSame(['Host' => 'example.org'], $headers);
				$this->assertSame('', $data);
				$this->assertSame('GET', $options['type']);

				return 'HTTP/1.1 404 Not Found' . "\r\n" .
				'Content-Type:text/plain' . "\r\n" .
				"\r\n" .
				'404 Not Found';
			}
		);

		$http_client = new HttpClient(
			[
				'transport' => $transport,
			]
		);

		$request = $http_client->createRequest('GET', 'https://example.org/not-found');

		$response = $http_client->sendRequest($request);

		$this->assertSame(404, $response->getStatusCode());
		$this->assertSame('Not Found', $response->getReasonPhrase());
		$this->assertSame('1.1', $response->getProtocolVersion());
		$this->assertSame(['content-type' => ['text/plain']], $response->getHeaders());
		$this->assertSame('404 Not Found', $response->getBody()->__toString());
	}

	/**
	 * Tests receiving a response when using sendRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::sendRequest
	 *
	 * @return void
	 */
	public function testSendRequestReturnsResponseOn503Error() {
		$transport = $this->createMock(Transport::class);
		$transport->expects($this->once())->method('request')->willReturnCallback(
			function ($url, $headers, $data, $options) use ($transport) {
				$this->assertSame('https://example.org/not-available', $url);
				$this->assertSame(['Host' => 'example.org'], $headers);
				$this->assertSame('', $data);
				$this->assertSame('GET', $options['type']);

				return 'HTTP/1.1 503 Service Unavailable' . "\r\n" .
				'Content-Type:text/plain' . "\r\n" .
				"\r\n" .
				'503 Service Unavailable';
			}
		);

		$http_client = new HttpClient(
			[
				'transport' => $transport,
			]
		);

		$request = $http_client->createRequest('GET', 'https://example.org/not-available');

		$response = $http_client->sendRequest($request);

		$this->assertSame(503, $response->getStatusCode());
		$this->assertSame('Service Unavailable', $response->getReasonPhrase());
		$this->assertSame('1.1', $response->getProtocolVersion());
		$this->assertSame(['content-type' => ['text/plain']], $response->getHeaders());
		$this->assertSame('503 Service Unavailable', $response->getBody()->__toString());
	}

	/**
	 * Tests receiving an exception when using sendRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::sendRequest
	 *
	 * @return void
	 */
	public function testSendRequestThrowsRequestException() {
		$transport = $this->createMock(Transport::class);

		$http_client = new HttpClient(
			[
				'transport' => $transport,
			]
		);

		$request = $http_client->createRequest('GET', '');

		$this->expectException(ClientException::class);
		$this->expectException(RequestException::class);
		$this->expectExceptionMessage('Only HTTP(S) requests are handled.');

		$http_client->sendRequest($request);
	}

	/**
	 * Tests receiving an exception when using sendRequest().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::sendRequest
	 *
	 * @return void
	 */
	public function testSendRequestThrowsNetworkException() {
		$e = new ExceptionTransport('error message', 'Unknown');

		$transport = $this->createMock(Transport::class);
		$transport->method('request')->willThrowException($e);

		$http_client = new HttpClient(
			[
				'transport' => $transport,
			]
		);

		$request = $http_client->createRequest('GET', 'https://example.org');

		$this->expectException(ClientException::class);
		$this->expectException(NetworkException::class);
		$this->expectExceptionMessage('error message');

		$http_client->sendRequest($request);
	}
}
