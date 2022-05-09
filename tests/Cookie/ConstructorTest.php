<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Cookie::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidName($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($name) must be of type string');

		new Cookie($input, 'value');
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$value`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidValue($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($value) must be of type string');

		new Cookie('name', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidStringInput() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidAttributes
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidAttributes($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #3 ($attributes) must be of type array|ArrayAccess&Traversable');

		new Cookie('name', 'value', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidAttributes() {
		$except = array_intersect(TypeProviderHelper::GROUP_ITERABLE, TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
		return TypeProviderHelper::getAllExcept($except);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$flags`.
	 *
	 * @dataProvider dataInvalidFlags
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidFlags($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #4 ($flags) must be of type array');

		new Cookie('name', 'value', [], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidFlags() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$reference_time`.
	 *
	 * @dataProvider dataInvalidReferenceTime
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidReferenceTime($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #5 ($reference_time) must be of type integer|null');

		new Cookie('name', 'value', [], [], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidReferenceTime() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_NULL, TypeProviderHelper::GROUP_INT);
	}
}
