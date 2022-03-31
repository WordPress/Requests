<?php

namespace WpOrg\Requests\Tests\Proxy\Http;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Proxy\Http;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Proxy\Http::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Tests receiving an exception when an invalid input type is passed to the Proxy\Http constructor.
	 *
	 * @dataProvider dataInvalidParameterType
	 *
	 * @param mixed $input Input to pass to the function.
	 *
	 * @return void
	 */
	public function testInvalidParameterType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($args) must be of type array|string|null');

		new Http($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidParameterType() {
		return TypeProviderHelper::getAllExcept(
			TypeProviderHelper::GROUP_NULL,
			TypeProviderHelper::GROUP_STRING,
			TypeProviderHelper::GROUP_ARRAY
		);
	}
}
