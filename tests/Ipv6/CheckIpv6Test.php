<?php

namespace WpOrg\Requests\Tests\Ipv6;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Ipv6;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * Test for the Ipv6::check_ipv6() method.
 *
 * Note: the "valid input type" tests can be removed once actual tests for the functionality
 * of the methods have been added.
 *
 * @covers \WpOrg\Requests\Ipv6::check_ipv6
 */
final class CheckIpv6Test extends TestCase {

	/**
	 * Tests receiving an exception when an invalid input type is passed to the Ipv6::check_ipv6() method.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $ip Parameter to test input validation with.
	 *
	 * @return void
	 */
	public function testInvalidInputType($ip) {
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

	/**
	 * Tests that the Ipv6::check_ipv6() method accepts string/stringable as an $ip parameter.
	 *
	 * @dataProvider dataValidInputType
	 *
	 * @param string $ip An IPv6 address
	 *
	 * @return void
	 */
	public function testValidInputType($ip) {
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
}
