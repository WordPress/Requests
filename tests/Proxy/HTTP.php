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

	public function transportProvider() {
		return array(
			array('Requests_Transport_cURL'),
			array('Requests_Transport_fsockopen'),
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
	 *
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Invalid number of arguments
	 */
	public function testConnectInvalidParameters($transport) {
		$this->checkProxyAvailable();

		$options = array(
			'proxy'     => array(REQUESTS_HTTP_PROXY, 'testuser', 'password', 'something'),
			'transport' => $transport,
		);
		Requests::get(httpbin('/get'), array(), $options);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testConnectWithInstance($transport) {
		$this->checkProxyAvailable();

		$options  = array(
			'proxy'     => new Requests_Proxy_HTTP(REQUESTS_HTTP_PROXY),
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
			&& $transport === 'Requests_Transport_fsockopen'
		) {
			// @TODO fsockopen connection times out on invalid auth instead of returning 407.
			if (method_exists($this, 'expectException')) {
				$this->expectException('Requests_Exception');
				$this->expectExceptionMessage('fsocket timed out');
			} else {
				$this->setExpectedException('Requests_Exception', 'fsocket timed out');
			}
		}

		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertSame(407, $response->status_code);
	}
}
