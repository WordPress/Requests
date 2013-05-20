<?php

class RequestsTest_Transport_fsockopen extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_fsockopen';

	public function testHTTPS() {
		// If OpenSSL isn't loaded, this should fail
		if (!defined('OPENSSL_VERSION_NUMBER')) {
			$this->setExpectedException('Requests_Exception');
		}

		return parent::testHTTPS();
	}
}
