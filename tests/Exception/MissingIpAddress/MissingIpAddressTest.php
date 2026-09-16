<?php

namespace WpOrg\Requests\Tests\Exception\MissingIpAddress;

use WpOrg\Requests\Exception\MissingIpAddress;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Exception\MissingIpAddress
 */
final class MissingIpAddressTest extends TestCase {

	/**
	 * Test that the exception is created correctly with the for_host() method.
	 *
	 * @return void
	 */
	public function testForHost() {
		$this->expectException(MissingIpAddress::class);
		$this->expectExceptionMessage('No IP address was found for host: example.com');

		throw MissingIpAddress::for_host('example.com');
	}
}
