<?php

namespace Requests\Tests\Proxy;

use Requests\Tests\TestCase;
use Requests_Transport_fsockopen;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Proxy\Http;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Transport\Curl;

class HttpTest extends TestCase {
	protected function checkProxyAvailable($type = '') {
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

	public function transportProvider() {
		return array(
			array(Curl::class),
			array(Requests_Transport_fsockopen::class),
		);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithString($transport) {
		$this->checkProxyAvailable();

		$options  = array(
			'proxy'     => REQUESTS_HTTP_PROXY,
			'transport' => $transport,
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertSame('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertSame('http', $data['headers']['x-requests-proxy']);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithArray($transport) {
		$this->checkProxyAvailable();

		$options  = array(
			'proxy'     => array(REQUESTS_HTTP_PROXY),
			'transport' => $transport,
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertSame('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertSame('http', $data['headers']['x-requests-proxy']);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectInvalidParameters($transport) {
		$this->checkProxyAvailable();

		$options = array(
			'proxy'     => array(REQUESTS_HTTP_PROXY, 'testuser', 'password', 'something'),
			'transport' => $transport,
		);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid number of arguments');
		Requests::get(httpbin('/get'), array(), $options);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithInstance($transport) {
		$this->checkProxyAvailable();

		$options  = array(
			'proxy'     => new Http(REQUESTS_HTTP_PROXY),
			'transport' => $transport,
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertSame('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertSame('http', $data['headers']['x-requests-proxy']);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithAuth($transport) {
		$this->checkProxyAvailable('auth');

		$options  = array(
			'proxy'     => array(
				REQUESTS_HTTP_PROXY_AUTH,
				REQUESTS_HTTP_PROXY_AUTH_USER,
				REQUESTS_HTTP_PROXY_AUTH_PASS,
			),
			'transport' => $transport,
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
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

		$options = array(
			'proxy'     => array(
				REQUESTS_HTTP_PROXY_AUTH,
				REQUESTS_HTTP_PROXY_AUTH_USER . '!',
				REQUESTS_HTTP_PROXY_AUTH_PASS . '!',
			),
			'transport' => $transport,
		);

		if (version_compare(phpversion(), '5.5.0', '>=') === true
			&& $transport === Requests_Transport_fsockopen::class
		) {
			// @TODO fsockopen connection times out on invalid auth instead of returning 407.
			$this->expectException(Exception::class);
			$this->expectExceptionMessage('fsocket timed out');
		}

		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertSame(407, $response->status_code);
	}
}
