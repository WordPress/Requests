<?php

abstract class RequestsTest_Transport_Base extends PHPUnit_Framework_TestCase {
	protected function getOptions($other = array()) {
		$options = array(
			'transport' => $this->transport
		);
		$options = array_merge($options, $other);
		return $options;
	}

	public function testSimpleGET() {
		$request = Requests::get('http://httpbin.org/get', array(), $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('http://httpbin.org/get', $result['url']);
		$this->assertEmpty($result['args']);
	}

	public function testGETWithArgs() {
		$request = Requests::get('http://httpbin.org/get?test=true&test2=test', array(), $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('http://httpbin.org/get?test=true&test2=test', $result['url']);
		$this->assertEquals(array('test' => 'true', 'test2' => 'test'), $result['args']);
	}

	public function testGETWithData() {
		$data = array(
			'test' => 'true',
			'test2' => 'test',
		);
		$request = Requests::request('http://httpbin.org/get', array(), $data, Requests::GET, $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('http://httpbin.org/get?test=true&test2=test', $result['url']);
		$this->assertEquals(array('test' => 'true', 'test2' => 'test'), $result['args']);
	}

	public function testGETWithDataAndQuery() {
		$data = array(
			'test2' => 'test',
		);
		$request = Requests::request('http://httpbin.org/get?test=true', array(), $data, Requests::GET, $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('http://httpbin.org/get?test=true&test2=test', $result['url']);
		$this->assertEquals(array('test' => 'true', 'test2' => 'test'), $result['args']);
	}

	public function testGETWithHeaders() {
		$headers = array(
			'Requested-At' => time(),
		);
		$request = Requests::get('http://httpbin.org/get', $headers, $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals($headers['Requested-At'], $result['headers']['Requested-At']);
	}

	public function testChunked() {
		$request = Requests::get('http://httpbin.org/stream/1', array(), $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('http://httpbin.org/stream/1', $result['url']);
		$this->assertEmpty($result['args']);
	}

	public function testHEAD() {
		$request = Requests::head('http://httpbin.org/get', array(), $this->getOptions());
		$this->assertEquals(200, $request->status_code);
		$this->assertEquals('', $request->body);
	}

	public function testRawPOST() {
		$data = 'test';
		$request = Requests::post('http://httpbin.org/post', array(), $data, $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('test', $result['data']);
	}

	public function testFormPost() {
		$data = 'test=true&test2=test';
		$request = Requests::post('http://httpbin.org/post', array(), $data, $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals(array('test' => 'true', 'test2' => 'test'), $result['form']);
	}

	public function testPOSTWithArray() {
		$data = array(
			'test' => 'true',
			'test2' => 'test',
		);
		$request = Requests::post('http://httpbin.org/post', array(), $data, $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals(array('test' => 'true', 'test2' => 'test'), $result['form']);
	}

	public function testRedirects() {
		$request = Requests::get('http://httpbin.org/redirect/6', array(), $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$this->assertEquals(6, $request->redirects);
	}

	public function testRelativeRedirects() {
		$request = Requests::get('http://httpbin.org/relative-redirect/6', array(), $this->getOptions());
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
		$request = Requests::get('http://httpbin.org/redirect/11', array(), $this->getOptions($options));
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
		$request = Requests::get($url, array(), $this->getOptions($options));
		$this->assertEquals($code, $request->status_code);
		$this->assertEquals($success, $request->success);
	}

	public function testGzipped() {
		$request = Requests::get('http://httpbin.org/gzip', array(), $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertEquals(true, $result->gzipped);
	}

	public function testStreamToFile() {
		$options = array(
			'filename' => tempnam(sys_get_temp_dir(), 'RLT') // RequestsLibraryTest
		);
		$request = Requests::get('http://httpbin.org/get', array(), $this->getOptions($options));
		$this->assertEquals(200, $request->status_code);
		$this->assertEmpty($request->body);

		$contents = file_get_contents($options['filename']);
		$result = json_decode($contents, true);
		$this->assertEquals('http://httpbin.org/get', $result['url']);
		$this->assertEmpty($result['args']);

		unlink($options['filename']);
	}

	public function testNonblocking() {
		$options = array(
			'blocking' => false
		);
		$request = Requests::get('http://httpbin.org/get', array(), $this->getOptions($options));
		$empty = new Requests_Response();
		$this->assertEquals($empty, $request);
	}

	/**
	 * @expectedException Requests_Exception
	 */
	public function testBadIP() {
		$request = Requests::get('http://256.256.256.0/', array(), $this->getOptions());
	}

	public function testHTTPS() {
		$request = Requests::get('https://httpbin.org/get', array(), $this->getOptions());
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body, true);
		$this->assertEquals('http://httpbin.org/get', $result['url']);
		$this->assertEmpty($result['args']);
	}

	/**
	 * @expectedException Requests_Exception
	 */
	/*public function testTimeout() {
		$options = array(
			'timeout' => 1,
		);
		$request = Requests::get('http://httpbin.org/get', array(), $this->getOptions($options));
		var_dump($request);
	}*/
}