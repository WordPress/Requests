<?php

class RequestsTest_Transport_fsockopen extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_fsockopen';

	public function testBadIP() {
		$this->expectException('Requests_Exception');
		parent::testBadIP();
	}

	public function testExpiredHTTPS() {
		$this->expectException('Requests_Exception');
		$this->expectExceptionMessage('SSL certificate did not match the requested domain name');
		parent::testExpiredHTTPS();
	}

	public function testRevokedHTTPS() {
		$this->expectException('Requests_Exception');
		$this->expectExceptionMessage('SSL certificate did not match the requested domain name');
		parent::testRevokedHTTPS();
	}

	/**
	 * Test that SSL fails with a bad certificate
	 */
	public function testBadDomain() {
		$this->expectException('Requests_Exception');
		$this->expectExceptionMessage('SSL certificate did not match the requested domain name');
		parent::testBadDomain();
	}

	public function testPoolNotImplementedInFsock() {
		$requests  = array(
			'test1' => array(
				'url' => httpbin('/get'),
			),
			'test2' => array(
				'url' => httpbin('/get'),
			),
		);
		$responses = Requests::request_pool($requests, $this->getOptions());
		$this->assertSame(array(), $responses);
	}

	/**
	 * Issue #248.
	 */
	public function testContentLengthHeader() {
		$hooks = new Requests_Hooks();
		$hooks->register('fsockopen.after_headers', array($this, 'checkContentLengthHeader'));

		Requests::post(httpbin('/post'), array(), array(), $this->getOptions(array('hooks' => $hooks)));
	}

	/**
	 * Issue #248.
	 */
	public function checkContentLengthHeader($headers) {
		$this->assertStringContainsString('Content-Length: 0', $headers);
	}
}
