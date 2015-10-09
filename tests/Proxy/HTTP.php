<?php

class RequestsTest_Proxy_HTTP extends PHPUnit_Framework_TestCase {
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
	public function testConnectWithString() {
		$this->checkProxyAvailable();

		$options = array(
			'proxy' => REQUESTS_HTTP_PROXY
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['x-requests-proxy']);
	}

	public function testConnectWithArray() {
		$this->checkProxyAvailable();

		$options = array(
			'proxy' => array(REQUESTS_HTTP_PROXY)
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['x-requests-proxy']);
	}

	/**
	 * @expectedException Requests_Exception
	 */
	public function testConnectInvalidParameters() {
		$this->checkProxyAvailable();

		$options = array(
			'proxy' => array(REQUESTS_HTTP_PROXY, 'testuser', 'password', 'something')
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
	}

	public function testConnectWithInstance() {
		$this->checkProxyAvailable();

		$options = array(
			'proxy' => REQUESTS_HTTP_PROXY
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['x-requests-proxy']);
	}

	public function testConnectWithAuth() {
		$this->checkProxyAvailable('auth');

		$options = array(
			'proxy' => array(
				REQUESTS_HTTP_PROXY_AUTH,
				REQUESTS_HTTP_PROXY_AUTH_USER,
				REQUESTS_HTTP_PROXY_AUTH_PASS
			),
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals(200, $response->status_code);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['x-requests-proxy']);
	}

	public function testConnectWithInvalidAuth() {
		$this->checkProxyAvailable('auth');

		$options = array(
			'proxy' => array(
				REQUESTS_HTTP_PROXY_AUTH,
				REQUESTS_HTTP_PROXY_AUTH_USER . '!',
				REQUESTS_HTTP_PROXY_AUTH_PASS . '!'
			),
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals(407, $response->status_code);
	}
}