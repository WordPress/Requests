<?php

namespace WpOrg\Requests\Tests\Transport\Curl;

use CurlHandle;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\Transport\BaseTestCase;
use WpOrg\Requests\Transport\Curl;

final class CurlTest extends BaseTestCase {
	protected $transport = Curl::class;

	/**
	 * Temporary storage of the cURL handle to assert against.
	 *
	 * @var null|resource|\CurlHandle
	 */
	protected $curl_handle;

	/**
	 * Get the options to use for the cURL tests.
	 *
	 * This adds a hook on curl.before_request to store the cURL handle. This is
	 * needed for asserting after the test scenarios that the cURL handle was
	 * correctly closed.
	 *
	 * @param array $other
	 * @return array
	 */
	protected function getOptions($other = []) {
		$options = parent::getOptions($other);

		$this->curl_handle = null;

		if (!array_key_exists('hooks', $options)) {
			$options['hooks'] = new Hooks();
		}

		$options['hooks']->register(
			'curl.before_request',
			function ($handle) {
				$this->curl_handle = $handle;
			}
		);

		return $options;
	}

	/**
	 * Post condition asserts to run after each scenario.
	 *
	 * This is used for asserting that cURL handles are not leaking memory.
	 */
	protected function assert_post_conditions() {
		if ($this->curl_handle === null) {
			// No cURL handle was used during this particular test scenario.
			return;
		}

		if ($this->curl_handle instanceof CurlHandle) {
			// CURL handles have been changed from resources into CurlHandle
			// objects starting with PHP 8.0, which don;t need to be closed.
			return;
		}

		if ($this->shouldClosedResourceAssertionBeSkipped($this->curl_handle) === false) {
			$this->assertIsClosedResource($this->curl_handle);
		}
	}

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
}
