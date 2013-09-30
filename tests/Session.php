<?php

class RequestsTest_Session extends PHPUnit_Framework_TestCase {
	public function testURLResolution() {
		$session = new Requests_Session('http://httpbin.org/');

		// Set the cookies up
		$response = $session->get('/get');
		$this->assertTrue($response->success);
		$this->assertEquals('http://httpbin.org/get', $response->url);

		$data = json_decode($response->body, true);
		$this->assertNotNull($data);
		$this->assertArrayHasKey('url', $data);
		$this->assertEquals('http://httpbin.org/get', $data['url']);
	}

	public function testSharedCookies() {
		$session = new Requests_Session('http://httpbin.org/');

		$options = array(
			'follow_redirects' => false
		);
		$response = $session->get('/cookies/set?requests-testcookie=testvalue', array(), $options);
		$this->assertEquals(302, $response->status_code);

		// Check the cookies
		$response = $session->get('/cookies');
		$this->assertTrue($response->success);

		// Check the response
		$data = json_decode($response->body, true);
		$this->assertNotNull($data);
		$this->assertArrayHasKey('cookies', $data);

		$cookies = array(
			'requests-testcookie' => 'testvalue'
		);
		$this->assertEquals($cookies, $data['cookies']);
	}
}
