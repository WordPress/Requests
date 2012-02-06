<?php

abstract class RequestsTest_Transport_Base extends PHPUnit_Framework_TestCase {
	public function testSimpleGET() {
		$request = Requests::get('http://httpbin.org/get');
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('http://httpbin.org/get', $result['url']);
		$this->assertEmpty($result['args']);
	}

	public function testGETWithArgs() {
		$request = Requests::get('http://httpbin.org/get?test=true&test2=test');
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('http://httpbin.org/get?test=true&test2=test', $result['url']);
		$this->assertEquals(array('test' => 'true', 'test2' => 'test'), $result['args']);
	}

	public function testHEAD() {
		$request = Requests::head('http://httpbin.org/get');
		$this->assertEquals(200, $request->status_code);
		$this->assertEquals('', $request->body);
	}

	public function testRawPOST() {
		$data = 'test';
		$request = Requests::post('http://httpbin.org/post', array(), $data);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('test', $result['data']);
	}

	public function testFormPost() {
		$data = 'test=true&test2=test';
		$request = Requests::post('http://httpbin.org/post', array(), $data);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals(array('test' => 'true', 'test2' => 'test'), $result['form']);
	}

	public function testRedirects() {
		$request = Requests::get('http://httpbin.org/redirect/6');
		$this->assertEquals(200, $request->status_code);

		$this->assertEquals(6, $request->redirects);
	}

	public function testRelativeRedirects() {
		$request = Requests::get('http://httpbin.org/relative-redirect/6');
		$this->assertEquals(200, $request->status_code);

		$this->assertEquals(6, $request->redirects);
	}

	/**
	 * @expectedException Requests_Exception
	 * @todo This should also check that the type is "toomanyredirects"
	 */
	public function testTooManyRedirects() {
		$options = array(
			'redirects' => 10, // default, but force just in case
		);
		$request = Requests::get('http://httpbin.org/redirect/11', array(), $options);
	}

	public static function statusCodeSuccessProvider() {
		return array(
			array(200, true),
			array(201, true),
			array(202, true),
			array(203, true),
			array(204, true),
			array(205, true),
			array(206, true),
			array(300, false),
			array(301, false),
			array(302, false),
			array(303, false),
			array(304, false),
			array(305, false),
			array(306, false),
			array(307, false),
			array(400, false),
			array(401, false),
			array(402, false),
			array(403, false),
			array(404, false),
			array(405, false),
			array(406, false),
			array(407, false),
			array(408, false),
			array(409, false),
			array(410, false),
			array(411, false),
			array(412, false),
			array(413, false),
			array(414, false),
			array(415, false),
			array(416, false),
			array(417, false),
			array(418, false), // RFC 2324
			array(500, false),
			array(501, false),
			array(502, false),
			array(503, false),
			array(504, false),
			array(505, false),
		);
	}

	/**
	 * @dataProvider statusCodeSuccessProvider
	 */
	public function testStatusCode($code, $success) {
		$url = sprintf('http://httpbin.org/status/%d', $code);
		$options = array(
			'follow_redirects' => false,
		);
		$request = Requests::get($url, array(), $options);
		$this->assertEquals($code, $request->status_code);
		$this->assertEquals($success, $request->success);
	}

	public function testGzipped() {
		$request = Requests::get('http://httpbin.org/gzip');
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertEquals(true, $result->gzipped);
	}
}