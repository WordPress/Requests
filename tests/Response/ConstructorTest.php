<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Response;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Verify that the constructor initializes two properties originally set as array to objects.
	 *
	 * @return void
	 */
	public function testInitialization() {
		$response = new Response();

		$this->assertInstanceof(Headers::class, $response->headers, 'Headers not initialized correctly');
		$this->assertInstanceof(Jar::class, $response->cookies, 'Cookies not initialized correctly');
	}
}
