<?php

namespace WpOrg\Requests\Tests\Proxy;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\ArgumentCount;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Proxy\Http;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Transport\Fsockopen;

final class HttpTest extends TestCase {
	private function checkProxyAvailable($type = '') {
		switch ($type) {
			case 'auth':
				$has_proxy = defined('REQUESTS_HTTP_PROXY_AUTH') && REQUESTS_HTTP_PROXY_AUTH;
				break;

			default:
				$has_proxy = defined('REQUESTS_HTTP_PROXY') && REQUESTS_HTTP_PROXY;
				break;
		}

		if (!$has_proxy) {
			$this->markTestSkipped('Proxy not available');
		}
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithString($transport) {
		$this->checkProxyAvailable();

		$options  = [
			'proxy'     => REQUESTS_HTTP_PROXY,
			'transport' => $transport,
		];
		$response = Requests::get(httpbin('/get'), [], $options);
		$this->assertSame('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertSame('http', $data['headers']['x-requests-proxy']);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithArray($transport) {
		$this->checkProxyAvailable();

		$options  = [
			'proxy'     => [REQUESTS_HTTP_PROXY],
			'transport' => $transport,
		];
		$response = Requests::get(httpbin('/get'), [], $options);
		$this->assertSame('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertSame('http', $data['headers']['x-requests-proxy']);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectInvalidParameters($transport) {
		$this->checkProxyAvailable();

		$options = [
			'proxy'     => [REQUESTS_HTTP_PROXY, 'testuser', 'password', 'something'],
			'transport' => $transport,
		];
		$this->expectException(ArgumentCount::class);
		$this->expectExceptionMessage('WpOrg\Requests\Proxy\Http::__construct() expects an array with exactly one element or exactly three elements');
		Requests::get(httpbin('/get'), [], $options);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithInstance($transport) {
		$this->checkProxyAvailable();

		$options  = [
			'proxy'     => new Http(REQUESTS_HTTP_PROXY),
			'transport' => $transport,
		];
		$response = Requests::get(httpbin('/get'), [], $options);
		$this->assertSame('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertSame('http', $data['headers']['x-requests-proxy']);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithAuth($transport) {
		$this->checkProxyAvailable('auth');

		$options  = [
			'proxy'     => [
				REQUESTS_HTTP_PROXY_AUTH,
				REQUESTS_HTTP_PROXY_AUTH_USER,
				REQUESTS_HTTP_PROXY_AUTH_PASS,
			],
			'transport' => $transport,
		];
		$response = Requests::get(httpbin('/get'), [], $options);
		$this->assertSame(200, $response->status_code);
		$this->assertSame('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertSame('http', $data['headers']['x-requests-proxy']);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithInvalidAuth($transport) {
		$this->checkProxyAvailable('auth');

		$options = [
			'proxy'     => [
				REQUESTS_HTTP_PROXY_AUTH,
				REQUESTS_HTTP_PROXY_AUTH_USER . '!',
				REQUESTS_HTTP_PROXY_AUTH_PASS . '!',
			],
			'transport' => $transport,
		];

		if (version_compare(phpversion(), '5.5.0', '>=') === true
			&& $transport === Fsockopen::class
		) {
			// @TODO fsockopen connection times out on invalid auth instead of returning 407.
			$this->expectException(Exception::class);
			$this->expectExceptionMessage('fsocket timed out');
		}

		$response = Requests::get(httpbin('/get'), [], $options);
		$this->assertSame(407, $response->status_code);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to the Proxy\Http constructor.
	 *
	 * @covers \WpOrg\Requests\Proxy\Http::__construct
	 */
	public function testConstructorInvalidParameterType() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($args) must be of type array|string|null');

		new Http(false);
	}
}
