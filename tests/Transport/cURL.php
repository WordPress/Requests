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
}
