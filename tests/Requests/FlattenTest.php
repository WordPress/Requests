<?php

namespace WpOrg\Requests\Tests\Requests;

use ArrayIterator;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Requests::flatten
 */
final class FlattenTest extends TestCase {

	/**
	 * Tests receiving an exception when an invalid input type is passed as `$dictionary` to the flatten() method.
	 *
	 * @dataProvider dataInvalidData
	 *
	 * @param mixed $input Invalid input.
	 *
	 * @return void
	 */
	public function testInvalidData($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($dictionary) must be of type iterable');

		Requests::flatten($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidData() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ITERABLE);
	}

	/**
	 * Tests flattening of data arrays.
	 *
	 * @dataProvider dataFlatten
	 *
	 * @param mixed $input Valid input.
	 *
	 * @return void
	 */
	public function testFlatten($input) {
		$expected = [
			0 => 'key1: value1',
			1 => 'key2: value2',
		];

		$this->assertSame($expected, Requests::flatten($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataFlatten() {
		$to_flatten = ['key1' => 'value1', 'key2' => 'value2'];

		return [
			'array'           => [$to_flatten],
			'iterable object' => [new ArrayIterator($to_flatten)],
		];
	}
}
