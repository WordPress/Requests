<?php

namespace WpOrg\Requests\Tests\Utility\HostBindings;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\HostBindings;

/**
 * @covers \WpOrg\Requests\Utility\HostBindings::__construct
 * @covers \WpOrg\Requests\Utility\HostBindings::validate
 */
final class ConstructorTest extends TestCase {

	/**
	 * Test that valid host bindings are accepted by the constructor.
	 *
	 * @return void
	 */
	public function testValidHostBindings() {
		$bindings = [
			'example.com' => ['93.184.216.34'],
			'localhost'   => ['127.0.0.1', '::1'],
		];

		$host_bindings = new HostBindings($bindings);
		$this->assertInstanceOf(HostBindings::class, $host_bindings);
	}

	/**
	 * Test that invalid types for host_bindings parameter throw an exception.
	 *
	 * @dataProvider dataInvalidTypes
	 *
	 * @param mixed $input Invalid input.
	 *
	 * @return void
	 */
	public function testInvalidTypes($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($host_bindings) must be of type array');

		new HostBindings($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidTypes() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Test that invalid host keys throw an exception.
	 *
	 * @dataProvider dataInvalidHostKeys
	 *
	 * @param mixed $key Invalid host key.
	 *
	 * @return void
	 */
	public function testInvalidHostKeys($key) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('$host_bindings (host)');

		new HostBindings([$key => ['127.0.0.1']]);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidHostKeys() {
		return TypeProviderHelper::getAllExcept(
			TypeProviderHelper::GROUP_STRING,
			TypeProviderHelper::GROUP_ARRAY,
			TypeProviderHelper::GROUP_OBJECT,
			TypeProviderHelper::GROUP_RESOURCE,
			TypeProviderHelper::GROUP_FLOAT,
			TypeProviderHelper::GROUP_NULL
		);
	}

	/**
	 * Test that invalid IP address arrays throw an exception.
	 *
	 * @dataProvider dataInvalidIpArrays
	 *
	 * @param mixed $ips Invalid IP array.
	 *
	 * @return void
	 */
	public function testInvalidIpArrays($ips) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('$host_bindings (ip address array)');

		new HostBindings(['example.com' => $ips]);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidIpArrays() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Test that invalid IP addresses throw an exception.
	 *
	 * @dataProvider dataInvalidIpAddresses
	 *
	 * @param mixed $ip Invalid IP address.
	 *
	 * @return void
	 */
	public function testInvalidIpAddresses($ip) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('$host_bindings (ip address)');

		new HostBindings(['example.com' => [$ip]]);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidIpAddresses() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Test that hostnames are rejected when IP validation is enabled (default).
	 *
	 * @return void
	 */
	public function testRejectsHostnameAsIp() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('invalid IP address format');

		new HostBindings(['example.com' => ['attacker.com']]);
	}

	/**
	 * Test that URLs are rejected when IP validation is enabled.
	 *
	 * @return void
	 */
	public function testRejectsUrlAsIp() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('invalid IP address format');

		new HostBindings(['example.com' => ['http://evil.com']]);
	}

	/**
	 * Test that invalid IPv4 formats are rejected.
	 *
	 * @dataProvider dataInvalidIpv4Formats
	 *
	 * @param string $invalid_ip Invalid IPv4 address.
	 *
	 * @return void
	 */
	public function testRejectsInvalidIpv4($invalid_ip) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('invalid IP address format');

		new HostBindings(['test.com' => [$invalid_ip]]);
	}

	/**
	 * Data Provider for invalid IPv4 formats.
	 *
	 * @return array
	 */
	public static function dataInvalidIpv4Formats() {
		return [
			'out of range'    => ['999.999.999.999'],
			'too many octets' => ['192.168.1.1.1'],
			'too few octets'  => ['192.168.1'],
			'letters'         => ['abc.def.ghi.jkl'],
			'mixed'           => ['192.168.1.abc'],
			'leading zero'    => ['192.168.001.1'],
		];
	}

	/**
	 * Test that invalid IPv6 formats are rejected.
	 *
	 * @dataProvider dataInvalidIpv6Formats
	 *
	 * @param string $invalid_ip Invalid IPv6 address.
	 *
	 * @return void
	 */
	public function testRejectsInvalidIpv6($invalid_ip) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('invalid IP address format');

		new HostBindings(['test.com' => [$invalid_ip]]);
	}

	/**
	 * Data Provider for invalid IPv6 formats.
	 *
	 * @return array
	 */
	public static function dataInvalidIpv6Formats() {
		return [
			'invalid hex'         => ['2001:0db8:85g3::8a2e:0370:7334'],
			'too many colons'     => ['2001:0db8:::8a2e'],
			'invalid abbrev'      => [':::1'],
			'out of range'        => ['fffff::1'],
			'invalid mixed'       => ['::ffff:999.999.999.999'],
		];
	}

	/**
	 * Test that empty strings are rejected.
	 *
	 * @return void
	 */
	public function testRejectsEmptyString() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('empty string');

		new HostBindings(['test.com' => ['']]);
	}

	/**
	 * Test that whitespace-only strings are rejected.
	 *
	 * @return void
	 */
	public function testRejectsWhitespaceOnlyString() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('empty string');

		new HostBindings(['test.com' => ['   ']]);
	}

	/**
	 * Test that valid IPv4 addresses are accepted (including private ranges).
	 *
	 * @dataProvider dataValidIpv4Addresses
	 *
	 * @param string $valid_ip Valid IPv4 address.
	 *
	 * @return void
	 */
	public function testAcceptsValidIpv4($valid_ip) {
		$bindings = new HostBindings(['test.com' => [$valid_ip]]);
		$this->assertInstanceOf(HostBindings::class, $bindings);
	}

	/**
	 * Data Provider for valid IPv4 addresses.
	 *
	 * @return array
	 */
	public static function dataValidIpv4Addresses() {
		return [
			'public 1'        => ['8.8.8.8'],
			'public 2'        => ['1.1.1.1'],
			'public 3'        => ['203.0.113.1'],
			'localhost'       => ['127.0.0.1'],
			'private 10.x'    => ['10.0.0.1'],
			'private 192.168' => ['192.168.1.1'],
			'private 172.16'  => ['172.16.0.1'],
			'all zeros'       => ['0.0.0.0'],
			'broadcast'       => ['255.255.255.255'],
			'link-local'      => ['169.254.1.1'],
		];
	}

	/**
	 * Test that valid IPv6 addresses are accepted.
	 *
	 * @dataProvider dataValidIpv6Addresses
	 *
	 * @param string $valid_ip Valid IPv6 address.
	 *
	 * @return void
	 */
	public function testAcceptsValidIpv6($valid_ip) {
		$bindings = new HostBindings(['test.com' => [$valid_ip]]);
		$this->assertInstanceOf(HostBindings::class, $bindings);
	}

	/**
	 * Data Provider for valid IPv6 addresses.
	 *
	 * @return array
	 */
	public static function dataValidIpv6Addresses() {
		return [
			'full format'   => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'],
			'compressed'    => ['2001:db8:85a3::8a2e:370:7334'],
			'localhost'     => ['::1'],
			'all zeros'     => ['::'],
			'ipv4 mapped'   => ['::ffff:192.0.2.1'],
			'link-local'    => ['fe80::1'],
			'unique local'  => ['fc00::1'],
		];
	}

	/**
	 * Test that whitespace is normalized (trimmed).
	 *
	 * @return void
	 */
	public function testNormalizesWhitespace() {
		$bindings = new HostBindings(['test.com' => ['  192.168.1.1  ', "\t10.0.0.1\n"]]);
		$this->assertInstanceOf(HostBindings::class, $bindings);

		$ips = $bindings->get_all_ips_for_host('test.com');
		$this->assertSame('192.168.1.1', $ips[0]);
		$this->assertSame('10.0.0.1', $ips[1]);
	}

	/**
	 * Test that mixed IPv4 and IPv6 addresses are accepted.
	 *
	 * @return void
	 */
	public function testAcceptsMixedIpVersions() {
		$bindings = new HostBindings(
			[
				'test.com' => ['192.168.1.1', '2001:db8::1', '10.0.0.1', '::1'],
			]
		);
		$this->assertInstanceOf(HostBindings::class, $bindings);
	}

	/**
	 * Test that invalid $validate_ips parameter type throws exception.
	 *
	 * @return void
	 */
	public function testInvalidValidateIpsType() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($validate_ips) must be of type boolean');

		new HostBindings(['test.com' => ['1.1.1.1']], 'false');
	}

	/**
	 * Test that integer zero as $validate_ips parameter throws exception.
	 *
	 * @return void
	 */
	public function testIntegerZeroAsValidateIps() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($validate_ips) must be of type boolean');

		new HostBindings(['test.com' => ['1.1.1.1']], 0);
	}

	/**
	 * Test that validation can be explicitly skipped.
	 *
	 * @return void
	 */
	public function testSkipValidationAcceptsNonIpString() {
		$bindings = new HostBindings(
			['example.com' => ['custom-value']],
			HostBindings::SKIP_IP_VALIDATION
		);

		$this->assertInstanceOf(HostBindings::class, $bindings);
		$this->assertSame('custom-value', $bindings->get_first_ip_for_host('example.com'));
	}

	/**
	 * Test that empty strings are still rejected even with validation skipped.
	 *
	 * @return void
	 */
	public function testSkipValidationStillRejectsEmpty() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('empty string');

		new HostBindings(
			['example.com' => ['']],
			HostBindings::SKIP_IP_VALIDATION
		);
	}

	/**
	 * Test that complex configuration with multiple hosts and IPs works.
	 *
	 * @return void
	 */
	public function testAcceptsComplexConfiguration() {
		$bindings = new HostBindings(
			[
				'api.example.com' => ['203.0.113.1', '2001:db8::1'],
				'localhost'       => ['127.0.0.1', '::1'],
				'internal.corp'   => ['10.0.0.5', '10.0.0.6'],
			]
		);

		$this->assertInstanceOf(HostBindings::class, $bindings);
	}
}
