<?php

class RequestsTest_Transport_cURL extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_cURL';

	public function testBadIP() {
		$this->setExpectedException('Requests_Exception', 't resolve host');
		parent::testBadIP();
	}

	public function testExpiredHTTPS() {
		$this->setExpectedException('Requests_Exception', 'certificate subject name');
		parent::testExpiredHTTPS();
	}

	public function testRevokedHTTPS() {
		$this->setExpectedException('Requests_Exception', 'certificate subject name');
		parent::testRevokedHTTPS();
	}

	/**
	 * Test that SSL fails with a bad certificate
	 */
	public function testBadDomain() {
		$this->setExpectedException('Reqeusts_Exception', 'certificate subject name');
		parent::testBadDomain();
	}
}
