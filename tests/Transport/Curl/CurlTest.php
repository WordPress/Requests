<?php

namespace WpOrg\Requests\Tests\Transport\Curl;

use WeakReference;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\Fixtures\TransportCloseDouble;
use WpOrg\Requests\Tests\Transport\BaseTestCase;
use WpOrg\Requests\Transport\Curl;

final class CurlTest extends BaseTestCase {
	protected $transport = Curl::class;

	public function testBadIP() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('t resolve host');
		parent::testBadIP();
	}

	public function testExpiredHTTPS() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('certificate subject name');
		parent::testExpiredHTTPS();
	}

	public function testRevokedHTTPS() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('certificate subject name');
		parent::testRevokedHTTPS();
	}

	/**
	 * Test that SSL fails with a bad certificate
	 */
	public function testBadDomain() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('certificate subject name');
		parent::testBadDomain();
	}

	/**
	 * @small
	 */
	public function testDoesntOverwriteExpectHeaderIfManuallySet() {
		$headers = [
			'Expect' => 'foo',
		];
		$request = Requests::post($this->httpbin('/post'), $headers, [], $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame($headers['Expect'], $result['headers']['Expect']);
	}

	/**
	 * @small
	 */
	public function testDoesntSetExpectHeaderIfBodyExactly1MbButProtocolIsnt11() {
		$options = [
			'protocol_version' => 1.0,
		];
		$request = Requests::post($this->httpbin('/post'), [], str_repeat('x', 1048576), $this->getOptions($options));

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderWithDefaultSettings() {
		$request = Requests::post($this->httpbin('/post'), [], [], $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderIfBodyIsANestedArrayLessThan1Mb() {
		$data    = [
			str_repeat('x', 148576),
			[
				str_repeat('x', 548576),
			],
		];
		$request = Requests::post($this->httpbin('/post'), [], $data, $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	public function testSetsExpectHeaderIfBodyIsExactlyA1MbString() {
		$request = Requests::post($this->httpbin('/post'), [], str_repeat('x', 1048576), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	public function testSetsExpectHeaderIfBodyIsANestedArrayGreaterThan1Mb() {
		$data    = [
			str_repeat('x', 148576),
			[
				str_repeat('x', 548576),
				[
					str_repeat('x', 648576),
				],
			],
		];
		$request = Requests::post($this->httpbin('/post'), [], $data, $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	public function testSetsExpectHeaderIfBodyExactly1Mb() {
		$request = Requests::post($this->httpbin('/post'), [], str_repeat('x', 1048576), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderIfBodySmallerThan1Mb() {
		$request = Requests::post($this->httpbin('/post'), [], str_repeat('x', 1048575), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	/**
	 * @requires PHP 7.4
	 */
	public function testCurlHandlerGetsClosedWhenThereAreNoErrors() {
		$transport = new Curl();
		$weakref   = WeakReference::create($transport);

		$options   = [
			'transport' => $transport,
			'blocking'  => true,
		];

		try {
			$response = Requests::post('https://humanmade.com/', [], [], $options);
		} catch(Exception $e) {}

		$this->assertSame(200, $response->status_code);

		var_dump(xdebug_debug_zval('transport'),
		xdebug_debug_zval('options'));
		unset($transport, $options);

		$this->assertNull($weakref->get());
	}

	/**
	 * @requires PHP 7.4
	 */
	public function testCurlHandlerGetsClosedEvenWhenThereAreErrors() {
		$transport = new Curl();
		$weakref   = WeakReference::create($transport);

		$options   = [
			'transport' => $transport,
			'blocking'  => true,
		];

		try {
			$response = Requests::post('http://localhost:43992', [], [], $options);
		} catch(Exception $e) {}

		var_dump(xdebug_debug_zval('transport'),
		xdebug_debug_zval('options'));
		unset($transport, $options);

		$this->assertNull($weakref->get());
	}
}
