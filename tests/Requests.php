<?php

class RequestsTest_Requests extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException Requests_Exception
	 */
	public function testInvalidProtocol() {
		$request = Requests::request('ftp://128.0.0.1/');
	}

	/**
	 * Standard response header parsing
	 */
	public function testHeaderParsing() {
		$transport = new RawTransport();
		$transport->data =
			"HTTP/1.0 200 OK\r\n".
			"Host: localhost\r\n".
			"Host: ambiguous\r\n".
			"Nospace:here\r\n".
			"Muchspace:  there   \r\n".
			"Empty:\r\n".
			"Empty2: \r\n".
			"Folded: one\r\n".
			"\ttwo\r\n".
			"  three\r\n\r\n".
			"stop\r\n";

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$expected = new Requests_Response_Headers();
		$expected['host'] = 'localhost,ambiguous';
		$expected['nospace'] = 'here';
		$expected['muchspace'] = 'there';
		$expected['empty'] = '';
		$expected['empty2'] = '';
		$expected['folded'] = 'one two  three';
		$this->assertEquals($expected, $response->headers);
	}

	/**
	 * Headers with only \n delimiting should be treated as if they're \r\n
	 */
	public function testHeaderOnlyLF() {
		$transport = new RawTransport();
		$transport->data = "HTTP/1.0 200 OK\r\nTest: value\nAnother-Test: value\r\n\r\n";

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$this->assertEquals('value', $response->headers['test']);
		$this->assertEquals('value', $response->headers['another-test']);
	}
}