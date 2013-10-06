<?php

class RequestsTest_Transport_fsockopen extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_fsockopen';

	protected $skip_https = false;
	public function setUp() {
		// If OpenSSL isn't loaded, this should fail
		if (!defined('OPENSSL_VERSION_NUMBER')) {
			$this->skip_https = true;
		}
	}
}
