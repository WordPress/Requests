<?php

namespace WpOrg\Requests\Tests\Exception\UnknownHost;

use WpOrg\Requests\Exception\UnknownHost;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Exception\UnknownHost
 */
final class UnknownHostTest extends TestCase {

	/**
	 * Test that the exception is created correctly with the for_host() method.
	 *
	 * @return void
	 */
	public function testForHost() {
		$this->expectException(UnknownHost::class);
		$this->expectExceptionMessage('Unknown host was requested from the host bindings collection: example.com');

		throw UnknownHost::for_host('example.com');
	}
}
