<?php

class RequestsTest_Transport_fsockopen extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_fsockopen';

	/**
	 * @expectedException Requests_Exception
	 */
	public function testBadIP() {
		parent::testBadIP();
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage SSL certificate did not match the requested domain name
	 */
	public function testExpiredHTTPS() {
		parent::testExpiredHTTPS();
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage SSL certificate did not match the requested domain name
	 */
	public function testRevokedHTTPS() {
		parent::testRevokedHTTPS();
	}

	/**
	 * Test that SSL fails with a bad certificate
	 *
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage SSL certificate did not match the requested domain name
	 */
	public function testBadDomain() {
		parent::testBadDomain();
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
		$this->assertContains('Content-Length: 0', $headers);
	}
}
