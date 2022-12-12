<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Requests;
use Yoast\PHPUnitPolyfills\TestCases\TestCase as Polyfill_TestCase;

abstract class TestCase extends Polyfill_TestCase {

	/**
	 * Retrieve a URL to use for testing.
	 *
	 * @param string $suffix The query path to add to the base URL.
	 * @param bool   $ssl    Whether to get the URL using the `http` or the `https` protocol.
	 *                       Defaults to `false`, which will result in a URL using `http`.
	 *
	 * @return string
	 */
	public function httpbin($suffix = '', $ssl = false) {
		$host = $ssl ? 'https://' . \REQUESTS_TEST_HOST_HTTPS : 'http://' . \REQUESTS_TEST_HOST_HTTP;
		return rtrim($host, '/') . '/' . ltrim($suffix, '/');
	}

	/**
	 * Data provider for use in tests which need to be run against all default supported transports.
	 *
	 * @var array
	 */
	public function transportProvider() {
		$data = [];

		foreach (Requests::DEFAULT_TRANSPORTS as $transport) {
			$name        = substr($transport, (strrpos($transport, '\\') + 1));
			$data[$name] = [$transport];
		}

		return $data;
	}
}
