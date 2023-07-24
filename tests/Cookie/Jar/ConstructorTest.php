<?php

namespace WpOrg\Requests\Tests\Cookie\Jar;

use ReflectionObject;
use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Cookie\Jar::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Tests receiving an exception when an invalid input type is passed to the class constructor.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($cookies) must be of type array');

		new Jar($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidInputType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Tests that valid data is accepted by the constructor and the property gets set.
	 *
	 * @dataProvider dataValidInputType
	 *
	 * @param mixed $input Valid parameter input.
	 *
	 * @return void
	 */
	public function testValidInputType($input) {
		$obj = new Jar($input);

		$reflection = new ReflectionObject($obj);
		$property   = $reflection->getProperty('cookies');
		$property->setAccessible(true);
		$property_value = $property->getValue($obj);
		$property->setAccessible(false);

		$this->assertSame($input, $property_value, 'Cookies property has not been set to expected value');
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataValidInputType() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_ARRAY);
	}
}
