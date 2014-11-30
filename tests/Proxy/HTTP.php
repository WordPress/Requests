<?php

class RequestsTest_Proxy_HTTP extends PHPUnit_Framework_TestCase {
	public function testConnectWithString() {
		$options = array(
			'proxy' => '127.0.0.1:8080'
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['X-Requests-Proxy']);
	}

	public function testConnectWithArray() {
		$options = array(
			'proxy' => array('127.0.0.1:8080')
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['X-Requests-Proxy']);
	}

	/**
	 * @expectedException Requests_Exception
	 */
	public function testConnectInvalidParameters() {
		$options = array(
			'proxy' => array('127.0.0.1:8080', 'testuser', 'password', 'something')
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
	}

	public function testConnectWithInstance() {
		$options = array(
			'proxy' => '127.0.0.1:8080'
		);
		$response = Requests::get(httpbin('/get'), array(), $options);
		$this->assertEquals('http', $response->headers['x-requests-proxied']);

		$data = json_decode($response->body, true);
		$this->assertEquals('http', $data['headers']['X-Requests-Proxy']);
	}
}