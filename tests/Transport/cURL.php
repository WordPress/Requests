<?php

class RequestsTest_Transport_cURL extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_cURL';

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage t resolve host
	 */
	public function testBadIP() {
		parent::testBadIP();
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage certificate subject name
	 */
	public function testExpiredHTTPS() {
		parent::testExpiredHTTPS();
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage certificate subject name
	 */
	public function testRevokedHTTPS() {
		parent::testRevokedHTTPS();
	}

	/**
	 * Test that SSL fails with a bad certificate
	 *
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage certificate subject name
	 */
	public function testBadDomain() {
		parent::testBadDomain();
	}
}
