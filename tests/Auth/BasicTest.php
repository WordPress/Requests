<?php

namespace WpOrg\Requests\Tests\Auth;

use WpOrg\Requests\Auth\Basic;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TestCase;

final class BasicTest extends TestCase {

	/**
	 * @dataProvider transportProvider
	 */
	public function testUsingArray($transport) {
		$this->skipWhenTransportNotAvailable($transport);

		$options = array(
			'auth'      => array('user', 'passwd'),
			'transport' => $transport,
		);
		$request = Requests::get(httpbin('/basic-auth/user/passwd'), array(), $options);
		$this->assertSame(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertTrue($result->authenticated);
		$this->assertSame('user', $result->user);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testUsingInstantiation($transport) {
		$this->skipWhenTransportNotAvailable($transport);

		$options = array(
			'auth'      => new Basic(array('user', 'passwd')),
			'transport' => $transport,
		);
		$request = Requests::get(httpbin('/basic-auth/user/passwd'), array(), $options);
		$this->assertSame(200, $request->status_code);

		$result = json_decode($request->body);
		$this->assertTrue($result->authenticated);
		$this->assertSame('user', $result->user);
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testPOSTUsingInstantiation($transport) {
		$this->skipWhenTransportNotAvailable($transport);

		$options = array(
			'auth'      => new Basic(array('user', 'passwd')),
			'transport' => $transport,
		);
		$data    = 'test';
		$request = Requests::post(httpbin('/post'), array(), $data, $options);
		$this->assertSame(200, $request->status_code);

		$result = json_decode($request->body);

		$auth = $result->headers->Authorization;
		$auth = explode(' ', $auth);

		$this->assertSame(base64_encode('user:passwd'), $auth[1]);
		$this->assertSame('test', $result->data);
	}

	public function testMissingPassword() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid number of arguments');
		new Basic(array('user'));
	}

	/**
	 * Helper function to skip select tests when the transport under test is not available.
	 *
	 * @param string $transport Transport to use.
	 *
	 * @return void
	 */
	public function skipWhenTransportNotAvailable($transport) {
		if (!$transport::test()) {
			$this->markTestSkipped('Transport "' . $transport . '" is not available');
		}
	}
}
