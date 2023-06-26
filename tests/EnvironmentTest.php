<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Tests\TestCase;

final class EnvironmentTest extends TestCase {

	/**
	 * Tests whether the HTTP test server is available.
	 *
	 * This makes sure that the build actually fails when the HTTP test
	 * server is not available, as all tests relying on it further down
	 * the line will just be marked as skipped.
	 *
	 * @return void
	 */
	public function testHttpTestServerAvailable() {
		$this->assertTrue(\REQUESTS_TEST_SERVER_HTTP_AVAILABLE);
	}

	/**
	 * Tests whether the HTTPS test server is available.
	 *
	 * This makes sure that the build actually fails when the HTTPS test
	 * server is not available, as all tests relying on it further down
	 * the line will just be marked as skipped.
	 *
	 * @return void
	 */
	public function testHttpsTestServerAvailable() {
		$this->assertTrue(\REQUESTS_TEST_SERVER_HTTPS_AVAILABLE);
	}
}
