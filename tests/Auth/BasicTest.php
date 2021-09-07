<?php

namespace WpOrg\Requests\Tests\Auth;

use WpOrg\Requests\Auth\Basic;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Auth\Basic
 */
final class BasicTest extends TestCase {

	/**
	 * @dataProvider transportProvider
	 *
	 * @param string $transport Transport to use.
	 *
	 * @return void
	 */
	public function testUsingArray($transport) {
		$this->skipWhenTransportNotAvailable($transport);

		$options = array(
			'auth'      => array('user', 'passwd'),
			'transport' => $transport,
		);
		$request = Requests::get(httpbin('/basic-auth/user/passwd'), array(), $options);

		// Verify the request succeeded.
		$this->assertInstanceOf(
			Response::class,
			$request,
			'GET request did not return an instance of `Requests\Response`'
		);
		$this->assertSame(
			200,
			$request->status_code,
			'GET request failed. Expected status: 200. Received status: ' . $request->status_code
		);

		// Verify the response confirms that the request was authenticated.
		$result = json_decode($request->body);
		$this->assertIsObject($result, 'Decoded response body is not an object');

		$this->assertObjectHasAttribute(
			'authenticated',
			$result,
			'Property "authenticated" not available in decoded response'
		);
		$this->assertTrue($result->authenticated, 'Authentication failed');

		$this->assertObjectHasAttribute(
			'user',
			$result,
			'Property "user" not available in decoded response'
		);
		$this->assertSame('user', $result->user, 'Unexpected value encountered for "user"');
	}

	/**
	 * @dataProvider transportProvider
	 *
	 * @param string $transport Transport to use.
	 *
	 * @return void
	 */
	public function testUsingInstantiation($transport) {
		$this->skipWhenTransportNotAvailable($transport);

		$options = array(
			'auth'      => new Basic(array('user', 'passwd')),
			'transport' => $transport,
		);
		$request = Requests::get(httpbin('/basic-auth/user/passwd'), array(), $options);

		// Verify the request succeeded.
		$this->assertInstanceOf(
			Response::class,
			$request,
			'GET request did not return an instance of `Requests\Response`'
		);
		$this->assertSame(
			200,
			$request->status_code,
			'GET request failed. Expected status: 200. Received status: ' . $request->status_code
		);

		// Verify the response confirms that the request was authenticated.
		$result = json_decode($request->body);
		$this->assertIsObject($result, 'Decoded response body is not an object');

		$this->assertObjectHasAttribute(
			'authenticated',
			$result,
			'Property "authenticated" not available in decoded response'
		);
		$this->assertTrue($result->authenticated, 'Authentication failed');

		$this->assertObjectHasAttribute(
			'user',
			$result,
			'Property "user" not available in decoded response'
		);
		$this->assertSame('user', $result->user, 'Unexpected value encountered for "user"');
	}

	/**
	 * @dataProvider transportProvider
	 *
	 * @param string $transport Transport to use.
	 *
	 * @return void
	 */
	public function testUsingInstantiationWithDelayedSettingOfCredentials($transport) {
		$this->skipWhenTransportNotAvailable($transport);

		$options = array(
			'auth'      => new Basic(),
			'transport' => $transport,
		);

		$options['auth']->user = 'user';
		$options['auth']->pass = 'passwd';
		$request               = Requests::get(httpbin('/basic-auth/user/passwd'), array(), $options);

		// Verify the request succeeded.
		$this->assertInstanceOf(
			Response::class,
			$request,
			'GET request did not return an instance of `Requests\Response`'
		);
		$this->assertSame(
			200,
			$request->status_code,
			'GET request failed. Expected status: 200. Received status: ' . $request->status_code
		);

		// Verify the response confirms that the request was authenticated.
		$result = json_decode($request->body);
		$this->assertIsObject($result, 'Decoded response body is not an object');

		$this->assertObjectHasAttribute(
			'authenticated',
			$result,
			'Property "authenticated" not available in decoded response'
		);
		$this->assertTrue($result->authenticated, 'Authentication failed');

		$this->assertObjectHasAttribute(
			'user',
			$result,
			'Property "user" not available in decoded response'
		);
		$this->assertSame('user', $result->user, 'Unexpected value encountered for "user"');
	}

	/**
	 * @dataProvider transportProvider
	 *
	 * @param string $transport Transport to use.
	 *
	 * @return void
	 */
	public function testPOSTUsingInstantiation($transport) {
		$this->skipWhenTransportNotAvailable($transport);

		$options = array(
			'auth'      => new Basic(array('user', 'passwd')),
			'transport' => $transport,
		);
		$data    = 'test';
		$request = Requests::post(httpbin('/post'), array(), $data, $options);

		// Verify the request succeeded.
		$this->assertInstanceOf(
			Response::class,
			$request,
			'POST request did not return an instance of `Requests\Response`'
		);
		$this->assertSame(
			200,
			$request->status_code,
			'POST request failed. Expected status: 200. Received status: ' . $request->status_code
		);

		// Verify the response confirms that the request was authenticated.
		$result = json_decode($request->body);

		$this->assertIsObject($result, 'Decoded response body is not an object');
		$this->assertObjectHasAttribute(
			'headers',
			$result,
			'Property "headers" not available in decoded response'
		);
		$this->assertObjectHasAttribute(
			'Authorization',
			$result->headers,
			'Property "headers->Authorization" not available in decoded response'
		);

		$auth = $result->headers->Authorization;
		$auth = explode(' ', $auth);
		$this->assertArrayHasKey(1, $auth, 'Authorization header failed to be split into two parts');
		$this->assertSame(base64_encode('user:passwd'), $auth[1], 'Unexpected authorization string in headers');

		$this->assertObjectHasAttribute(
			'data',
			$result,
			'Property "data" not available in decoded response'
		);
		$this->assertSame('test', $result->data, 'Unexpected data value encountered');
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($args) must be of type array|null');

		new Basic($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInputType() {
		return array(
			'boolean false'         => array(false),
			'authentication string' => array('user:psw'),
		);
	}

	/**
	 * Verify that an exception is thrown when the class is instantiated with an invalid number of arguments.
	 *
	 * @dataProvider dataInvalidArgumentCount
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testInvalidArgumentCount($input) {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid number of arguments');

		new Basic($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidArgumentCount() {
		return array(
			'empty array'                 => array(array()),
			'array with only one element' => array(array('user')),
			'array with extra element'    => array(array('user', 'psw', 'port')),
		);
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
