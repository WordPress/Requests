<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

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
		$transport->expects($this->once())->method('request')->willReturnCallback(function ($url, $headers, $data, $options) use ($transport) {
			$this->assertSame('https://example.org/', $url);
			$this->assertSame(['Host' => ['example.org']], $headers);
			$this->assertSame('', $data);
			$this->assertSame('GET', $options['type']);

			return
				'HTTP/1.1 200 OK' . "\r\n".
				'Content-Type:text/plain'. "\r\n".
				"\r\n".
				'foobar';
		});

		$httpClient = new HttpClient([
			'transport' => $transport,
		]);

		$request = $httpClient->createRequest('GET', 'https://example.org');

		$response = $httpClient->sendRequest($request);

		$this->assertSame(200, $response->getStatusCode());
		$this->assertSame('OK', $response->getReasonPhrase());
		$this->assertSame('1.1', $response->getProtocolVersion());
		$this->assertSame(['content-type' => ['text/plain']], $response->getHeaders());
		$this->assertSame('foobar', $response->getBody()->__toString());
	}
}
