<?php

namespace WpOrg\Requests\Tests\Transport;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;
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
		$headers = array(
			'Expect' => 'foo',
		);
		$request = Requests::post(httpbin('/post'), $headers, array(), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame($headers['Expect'], $result['headers']['Expect']);
	}

	/**
	 * @small
	 */
	public function testDoesntSetExpectHeaderIfBodyExactly1MbButProtocolIsnt11() {
		$options = array(
			'protocol_version' => 1.0,
		);
		$request = Requests::post(httpbin('/post'), array(), str_repeat('x', 1048576), $this->getOptions($options));

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderWithDefaultSettings() {
		$request = Requests::post(httpbin('/post'), array(), array(), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderIfBodyIsANestedArrayLessThan1Mb() {
		$data    = array(
			str_repeat('x', 148576),
			array(
				str_repeat('x', 548576),
			),
		);
		$request = Requests::post(httpbin('/post'), array(), $data, $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}

	public function testSetsExpectHeaderIfBodyIsExactlyA1MbString() {
		$request = Requests::post(httpbin('/post'), array(), str_repeat('x', 1048576), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	public function testSetsExpectHeaderIfBodyIsANestedArrayGreaterThan1Mb() {
		$data    = array(
			str_repeat('x', 148576),
			array(
				str_repeat('x', 548576),
				array(
					str_repeat('x', 648576),
				),
			),
		);
		$request = Requests::post(httpbin('/post'), array(), $data, $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	public function testSetsExpectHeaderIfBodyExactly1Mb() {
		$request = Requests::post(httpbin('/post'), array(), str_repeat('x', 1048576), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertSame('100-Continue', $result['headers']['Expect']);
	}

	/**
	 * @small
	 */
	public function testSetsEmptyExpectHeaderIfBodySmallerThan1Mb() {
		$request = Requests::post(httpbin('/post'), array(), str_repeat('x', 1048575), $this->getOptions());

		$result = json_decode($request->body, true);

		$this->assertFalse(isset($result['headers']['Expect']));
	}
}
