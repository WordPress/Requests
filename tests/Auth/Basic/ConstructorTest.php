<?php

namespace WpOrg\Requests\Tests\Auth\Basic;

use WpOrg\Requests\Auth\Basic;
use WpOrg\Requests\Exception\ArgumentCount;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Auth\Basic::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Tests receiving an exception when an invalid input type is passed.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($args) must be of type array|null');

		new Basic($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInputType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_NULL, TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Verify that an exception is thrown when the class is instantiated with an invalid number of arguments.
	 *
	 * @dataProvider dataInvalidArgumentCount
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testInvalidArgumentCount($input) {
		$this->expectException(ArgumentCount::class);
		$this->expectExceptionMessage('WpOrg\Requests\Auth\Basic::__construct() expects an array with exactly two elements');

		new Basic($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidArgumentCount() {
		return [
			'empty array'                 => [[]],
			'array with only one element' => [['user']],
			'array with extra element'    => [['user', 'psw', 'port']],
		];
	}

	/**
	 * Tests valid instantiation of the class with a user and password.
	 *
	 * @return void
	 */
	public function testInstantiateWithValidInput() {
		$instance = new Basic(['user', 'psw']);

		$this->assertSame('user', $instance->user);
		$this->assertSame('psw', $instance->pass);
	}

	/**
	 * Tests valid instantiation of the class with passing any parameters.
	 *
	 * @return void
	 */
	public function testInstantiateWithNoInput() {
		$instance = new Basic();

		$this->assertNull($instance->user);
		$this->assertNull($instance->pass);
	}
}
