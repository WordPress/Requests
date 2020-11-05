<?php

class RequestsTest_Transport_fsockopen extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_fsockopen';

    public function testBadIP() {
        $this->setExpectedException('Requests_Exception');
        parent::testBadIP();
	}

    public function testExpiredHTTPS() {
        $this->setExpectedException('Requests_Exception', 'SSL certificate did not match the requested domain name');
        parent::testExpiredHTTPS();
	}

    public function testRevokedHTTPS() {
        $this->setExpectedException('Requests_Exception', 'SSL certificate did not match the requested domain name');
        parent::testRevokedHTTPS();
	}

	/**
	 * Test that SSL fails with a bad certificate
     */
	public function testBadDomain() {
        $this->setExpectedException('Requests_Exception', 'SSL certificate did not match the requested domain name');
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
