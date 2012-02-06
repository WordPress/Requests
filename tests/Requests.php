<?php

class RequestsTest_Requests extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException Requests_Exception
	 */
	public function testInvalidProtocol() {
		$request = Requests::request('ftp://128.0.0.1/');
	}
}