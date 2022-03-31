<?php

namespace WpOrg\Requests\Tests\Response\Headers;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Response\Headers::flatten
 */
final class FlattenTest extends TestCase {

	/**
	 * Tests flattening of data.
	 *
	 * @dataProvider dataFlatten
	 *
	 * @param string|array $input    Value to flatten.
	 * @param string       $expected Expected output value.
	 *
	 * @return void
	 */
	public function testFlatten($input, $expected) {
		$headers = new Headers();
		$this->assertSame($expected, $headers->flatten($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataFlatten() {
		return [
			'string'            => ['text', 'text'],
			'empty array'       => [[], ''],
			'array with values' => [['text', 10, 'more text'], 'text,10,more text'],
		];
	}

	/**
	 * Tests receiving an exception when an invalid value is passed to flatten().
	 *
	 * @dataProvider dataInvalidValue
	 *
	 * @param mixed $input Value to flatten.
	 *
	 * @return void
	 */
	public function testInvalidValue($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($value) must be of type string|array');

		$headers = new Headers();
		$headers->flatten($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidValue() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING, TypeProviderHelper::GROUP_ARRAY);
	}
}
