<?php

class RequestsTest_Transport_cURL extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_cURL';

	public function testBadIP() {
		$this->expectException('Requests_Exception');
		$this->expectExceptionMessage('t resolve host');
		parent::testBadIP();
	}

	public function testExpiredHTTPS() {
		$this->expectException('Requests_Exception');
		$this->expectExceptionMessage('certificate subject name');
		parent::testExpiredHTTPS();
	}

	public function testPoolMultiple() {
		$requests  = array(
			'test1' => array(
				'url' => httpbin('/get'),
			),
			'test2' => array(
				'url' => httpbin('/get'),
			),
		);
		$responses = Requests::request_pool($requests, $this->getOptions());

		// test1
		$this->assertNotEmpty($responses['test1']);
		$this->assertInstanceOf('Requests_Response', $responses['test1']);
		$this->assertSame(200, $responses['test1']->status_code);

		$result = json_decode($responses['test1']->body, true);
		$this->assertSame(httpbin('/get'), $result['url']);
		$this->assertEmpty($result['args']);

		// test2
		$this->assertNotEmpty($responses['test2']);
		$this->assertInstanceOf('Requests_Response', $responses['test2']);
		$this->assertSame(200, $responses['test2']->status_code);

		$result = json_decode($responses['test2']->body, true);
		$this->assertSame(httpbin('/get'), $result['url']);
		$this->assertEmpty($result['args']);
	}

	public function testPoolWithDifferingMethods() {
		$requests  = array(
			'get' => array(
				'url' => httpbin('/get'),
			),
			'post' => array(
				'url'  => httpbin('/post'),
				'type' => Requests::POST,
				'data' => 'test',
			),
		);
		$responses = Requests::request_pool($requests, $this->getOptions());

		// get
		$this->assertSame(200, $responses['get']->status_code);

		// post
		$this->assertSame(200, $responses['post']->status_code);
		$result = json_decode($responses['post']->body, true);
		$this->assertSame('test', $result['data']);
	}

	public function testPoolUsingCallbackAndFailure() {
		$requests        = array(
			'success' => array(
				'url' => httpbin('/get'),
			),
			'timeout' => array(
				'url'     => httpbin('/delay/10'),
				'options' => array(
					'timeout' => 1,
				),
			),
		);
		$this->completed = array();
		$options         = array(
			'complete' => array($this, 'completeCallback'),
		);
		$responses       = Requests::request_pool($requests, $this->getOptions($options));

		$this->assertSame($this->completed, $responses);
		$this->completed = array();
	}

	public function testPoolUsingCallback() {
		$requests        = array(
			'get' => array(
				'url' => httpbin('/get'),
			),
			'post' => array(
				'url'  => httpbin('/post'),
				'type' => Requests::POST,
				'data' => 'test',
			),
		);
		$this->completed = array();
		$options         = array(
			'complete' => array($this, 'completeCallback'),
		);
		$responses       = Requests::request_pool($requests, $this->getOptions($options));

		$this->assertSame($this->completed, $responses);
		$this->completed = array();
	}

	public function testPoolToFile() {
		$requests = array(
			'get' => array(
				'url'     => httpbin('/get'),
				'options' => array(
					'filename' => tempnam(sys_get_temp_dir(), 'RLT'), // RequestsLibraryTest
				),
			),
			'post' => array(
				'url'     => httpbin('/post'),
				'type'    => Requests::POST,
				'data'    => 'test',
				'options' => array(
					'filename' => tempnam(sys_get_temp_dir(), 'RLT'), // RequestsLibraryTest
				),
			),
		);
		Requests::request_pool($requests, $this->getOptions());

		// GET request
		$contents = file_get_contents($requests['get']['options']['filename']);
		$result   = json_decode($contents, true);
		$this->assertSame(httpbin('/get'), $result['url']);
		$this->assertEmpty($result['args']);
		unlink($requests['get']['options']['filename']);

		// POST request
		$contents = file_get_contents($requests['post']['options']['filename']);
		$result   = json_decode($contents, true);
		$this->assertSame(httpbin('/post'), $result['url']);
		$this->assertSame('test', $result['data']);
		unlink($requests['post']['options']['filename']);
	}

	public function testRevokedHTTPS() {
		$this->expectException('Requests_Exception');
		$this->expectExceptionMessage('certificate subject name');
		parent::testRevokedHTTPS();
	}

	/**
	 * Test that SSL fails with a bad certificate
	 */
	public function testBadDomain() {
		$this->expectException('Requests_Exception');
		$this->expectExceptionMessage('certificate subject name');
		parent::testBadDomain();
	}

	/**
	 * @small
	 */
	public function testDoesntOverwriteExpectHeaderIfManuallySet() {
		$headers = array(
			'Expect' => 'foo',
		);
		$request = Requests::post(httpbin('/post'), $headers, array(), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame($headers['Expect'], $result['headers']['Expect']);
	}

	/**
	 * @small
	 */
	public function testDoesntSetExpectHeaderIfBodyExactly1MbButProtocolIsnt11() {
		$options = array(
			'protocol_version' => 1.0,
		);
		$request = Requests::post(httpbin('/post'), array(), str_repeat('x', 1048576), $this->getOptions($options));

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderWithDefaultSettings() {
		$request = Requests::post(httpbin('/post'), array(), array(), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderIfBodyIsANestedArrayLessThan1Mb() {
		$data    = array(
			str_repeat('x', 148576),
			array(
				str_repeat('x', 548576),
			),
		);
		$request = Requests::post(httpbin('/post'), array(), $data, $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	public function testSetsExpectHeaderIfBodyIsExactlyA1MbString() {
		$request = Requests::post(httpbin('/post'), array(), str_repeat('x', 1048576), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	public function testSetsExpectHeaderIfBodyIsANestedArrayGreaterThan1Mb() {
		$data    = array(
			str_repeat('x', 148576),
			array(
				str_repeat('x', 548576),
				array(
					str_repeat('x', 648576),
				),
			),
		);
		$request = Requests::post(httpbin('/post'), array(), $data, $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	public function testSetsExpectHeaderIfBodyExactly1Mb() {
		$request = Requests::post(httpbin('/post'), array(), str_repeat('x', 1048576), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderIfBodySmallerThan1Mb() {
		$request = Requests::post(httpbin('/post'), array(), str_repeat('x', 1048575), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}
}
