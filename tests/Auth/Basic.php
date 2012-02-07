<?php

class RequestsTest_Auth_Basic extends PHPUnit_Framework_TestCase {
	public static function transportProvider() {
		$transports = array(
			array('Requests_Transport_fsockopen'),
			array('Requests_Transport_cURL'),
		);
		return $transports;
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testUsingArray($transport) {
		$options = array(
			'auth' => array('user', 'passwd'),
			'transport' => $transport,
		);
		$request = Requests::get('http://httpbin.org/basic-auth/user/passwd', array(), $options);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertEquals(true, $result->authenticated);
		$this->assertEquals('user', $result->user);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testUsingInstantiation($transport) {
		$options = array(
			'auth' => new Requests_Auth_Basic(array('user', 'passwd')),
			'transport' => $transport,
		);
		$request = Requests::get('http://httpbin.org/basic-auth/user/passwd', array(), $options);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertEquals(true, $result->authenticated);
		$this->assertEquals('user', $result->user);
	}

	/**
	 * @expectedException Requests_Exception
	 */
	public function testMissingPassword() {
		$auth = new Requests_Auth_Basic(array('user'));
	}
}