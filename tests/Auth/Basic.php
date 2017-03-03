<?php
namespace Rmccue\RequestTests\Auth;

use \Rmccue\Requests as Requests;
use \PHPUnit\Framework\TestCase as TestCase;

class Basic extends TestCase {
	public static function transportProvider() {
		$transports = array(
			array('\\Rmccue\\Requests\\Transport\\fsockopen'),
			array('\\Rmccue\\Requests\\Transport\\cURL'),
		);
		return $transports;
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testUsingArray($transport) {
		if (!call_user_func(array($transport, 'test'))) {
			$this->markTestSkipped($transport . ' is not available');
			return;
		}

		$options = array(
			'auth' => array('user', 'passwd'),
			'transport' => $transport,
		);
		$request = Requests::get(\Rmccue\RequestTests\httpbin('/basic-auth/user/passwd'), array(), $options);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertEquals(true, $result->authenticated);
		$this->assertEquals('user', $result->user);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testUsingInstantiation($transport) {
		if (!call_user_func(array($transport, 'test'))) {
			$this->markTestSkipped($transport . ' is not available');
			return;
		}

		$options = array(
			'auth' => new Requests\Auth\Basic(array('user', 'passwd')),
			'transport' => $transport,
		);
		$request = Requests::get(\Rmccue\RequestTests\httpbin('/basic-auth/user/passwd'), array(), $options);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertEquals(true, $result->authenticated);
		$this->assertEquals('user', $result->user);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testPOSTUsingInstantiation($transport) {
		if (!call_user_func(array($transport, 'test'))) {
			$this->markTestSkipped($transport . ' is not available');
			return;
		}

		$options = array(
			'auth' => new Requests\Auth\Basic(array('user', 'passwd')),
			'transport' => $transport,
		);
		$data = 'test';
		$request = Requests::post(\Rmccue\RequestTests\httpbin('/post'), array(), $data, $options);
		$this->assertEquals(200, $request->status_code);

		$result = json_decode($request->body);

		$auth = $result->headers->Authorization;
		$auth = explode(' ', $auth);

		$this->assertEquals(base64_encode('user:passwd'), $auth[1]);
        $this->assertEquals('test', $result->data);
	}

	/**
	 * @expectedException Rmccue\Requests\Exception
	 */
	public function testMissingPassword() {
		$auth = new Requests\Auth\Basic(array('user'));
	}

}