<?php

Requests::$transport = 'Requests_Transport_fsockopen';

class BasicAuthTest extends PHPUnit_Framework_TestCase {
	public function testUsingArray() {
		$options = array(
			'auth' => array('user', 'passwd')
		);
		$request = Requests::get('http://httpbin.org/basic-auth/user/passwd', array(), $options);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertEquals(true, $result->authenticated);
		$this->assertEquals('user', $result->user);
	}

	public function testUsingInstantiation() {
		$options = array(
			'auth' => new Requests_Auth_Basic(array('user', 'passwd'))
		);
		$request = Requests::get('http://httpbin.org/basic-auth/user/passwd', array(), $options);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertEquals(true, $result->authenticated);
		$this->assertEquals('user', $result->user);
	}
}