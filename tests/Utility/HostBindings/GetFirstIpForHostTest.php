<?php

namespace WpOrg\Requests\Tests\Utility\HostBindings;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Exception\MissingIpAddress;
use WpOrg\Requests\Exception\UnknownHost;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\HostBindings;

/**
 * @covers \WpOrg\Requests\Utility\HostBindings::get_first_ip_for_host
 */
final class GetFirstIpForHostTest extends TestCase {

	/**
	 * Test getting the first IP for existing hosts.
	 *
	 * @return void
	 */
	public function testGetFirstIpForExistingHost() {
		$bindings = [
			'example.com' => ['93.184.216.34', '93.184.216.35'],
			'localhost'   => ['127.0.0.1'],
		];

		$host_bindings = new HostBindings($bindings);

		$this->assertSame('93.184.216.34', $host_bindings->get_first_ip_for_host('example.com'));
		$this->assertSame('127.0.0.1', $host_bindings->get_first_ip_for_host('localhost'));
	}

	/**
	 * Test that requesting an unknown host throws an exception.
	 *
	 * @return void
	 */
	public function testUnknownHostThrowsException() {
		$this->expectException(UnknownHost::class);

		$host_bindings = new HostBindings(['example.com' => ['127.0.0.1']]);
		$host_bindings->get_first_ip_for_host('nonexistent.com');
	}

	/**
	 * Test that a host with no IPs throws an exception.
	 *
	 * @return void
	 */
	public function testEmptyIpArrayThrowsException() {
		$this->expectException(MissingIpAddress::class);

		$host_bindings = new HostBindings(['example.com' => []]);
		$host_bindings->get_first_ip_for_host('example.com');
	}

	/**
	 * Test that invalid host parameter types throw an exception.
	 *
	 * @dataProvider dataInvalidTypes
	 *
	 * @param mixed $input Invalid input.
	 *
	 * @return void
	 */
	public function testInvalidHostType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('$host');

		$host_bindings = new HostBindings(['example.com' => ['127.0.0.1']]);
		$host_bindings->get_first_ip_for_host($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidTypes() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}
}
