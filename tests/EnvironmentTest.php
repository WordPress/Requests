<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\Fixtures\RawTransportMock;
use WpOrg\Requests\Tests\Fixtures\TransportMock;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class EnvironmentTest extends TestCase {

	/**
	 * Tests whether the HTTP test server is available.
	 *
	 * @return void
	 */
	public function testHttpTestServerAvailable() {
		$this->assertTrue(\REQUESTS_TEST_SERVER_HTTP_AVAILABLE);
	}

	/**
	 * Tests whether the HTTPS test server is available.
	 *
	 * @return void
	 */
	public function testHttpsTestServerAvailable() {
		$this->assertTrue(\REQUESTS_TEST_SERVER_HTTPS_AVAILABLE);
	}
}
