<?php

class RequestsTest_Proxy_HTTP extends PHPUnit_Framework_TestCase {
	public function setUp() {
		parent::setUp();
		if (!defined('REQUESTS_HTTP_PROXY') || !REQUESTS_HTTP_PROXY) {
			$this->markTestSkipped('Proxy not available');
		}
	}

	public function testConnectWithString() {
		$options = array(
			'proxy' => REQUESTS_HTTP_PROXY
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['x-requests-proxy']);
	}

	public function testConnectWithArray() {
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
		$options = array(
			'proxy' => array(REQUESTS_HTTP_PROXY, 'testuser', 'password', 'something')
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
	}

	public function testConnectWithInstance() {
		$options = array(
			'proxy' => REQUESTS_HTTP_PROXY
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['x-requests-proxy']);
	}
}