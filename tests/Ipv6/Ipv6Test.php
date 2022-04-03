<?php

namespace WpOrg\Requests\Tests\Ipv6;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Ipv6;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * Test for the Ipv6 class.
 *
 * Note: the "valid input type" tests can be removed once actual tests for the functionality
 * of the methods have been added.
 *
 * @coversDefaultClass \WpOrg\Requests\Ipv6
 */
final class Ipv6Test extends TestCase {

	/**
	 * Tests that the Ipv6::check_ipv6() method accepts string/stringable as an $ip parameter.
	 *
	 * @covers       ::check_ipv6
	 * @dataProvider dataValidInputType
	 *
	 * @param string $ip An IPv6 address
	 *
	 * @return void
	 */
	public function testCheckIpv6ValidInputType($ip) {
		$this->assertIsBool(Ipv6::check_ipv6($ip));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataValidInputType() {
		return [
			'string'     => ['::1'],
			'stringable' => [new StringableObject('0:1234:dc0:41:216:3eff:fe67:3e01')],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to the Ipv6::check_ipv6() method.
	 *
	 * @covers       ::check_ipv6
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $ip Parameter to test input validation with.
	 *
	 * @return void
	 */
	public function testCheckIpv6InvalidInputType($ip) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($ip) must be of type string|Stringable');

		Ipv6::check_ipv6($ip);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInputType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRINGABLE);
	}
}
