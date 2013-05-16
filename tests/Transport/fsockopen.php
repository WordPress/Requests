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

	public function testHostHeader() {
		$hooks = new Requests_Hooks();
		$hooks->register('fsockopen.after_headers', array($this, 'headerParseCallback'));

		$request = Requests::get(
			'http://portquiz.positon.org:8080/',
			array(),
			$this->getOptions(array('hooks' => $hooks))
		);
	}

	public function headerParseCallback($transport) {
		preg_match('/Host:\s+(.+)\r\n/m', $transport, $headerMatch);
		$this->assertEquals('portquiz.positon.org:8080', $headerMatch[1]);
	}
}
