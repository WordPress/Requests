<?php

namespace WpOrg\Requests\Tests\Utility\FilteredIterator;

use ReflectionObject;
use stdClass;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\FilteredIterator;

/**
 * @covers \WpOrg\Requests\Utility\FilteredIterator::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Tests that valid $data is accepted by the constructor.
	 *
	 * @dataProvider dataValidData
	 *
	 * @param mixed $input Valid input.
	 *
	 * @return void
	 */
	public function testValidData($input) {
		$this->assertInstanceOf(FilteredIterator::class, new FilteredIterator($input, 'ltrim'));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataValidData() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_ITERABLE);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as `$data` to the constructor.
	 *
	 * @dataProvider dataInvalidData
	 *
	 * @param mixed $input Invalid input.
	 *
	 * @return void
	 */
	public function testInvalidData($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($data) must be of type iterable');

		new FilteredIterator($input, 'ltrim');
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidData() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ITERABLE);
	}

	/**
	 * Tests that valid $callback is accepted by the constructor.
	 *
	 * @dataProvider dataValidCallback
	 *
	 * @param mixed $input Valid input.
	 *
	 * @return void
	 */
	public function testValidCallback($input) {
		$obj = new FilteredIterator([], $input);

		$reflection = new ReflectionObject($obj);
		$property   = $reflection->getProperty('callback');
		$property->setAccessible(true);
		$callback_value = $property->getValue($obj);
		$property->setAccessible(false);

		$this->assertSame($input, $callback_value, 'Callback property has not been set');
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataValidCallback() {
		return [
			'existing PHP native function' => ['strtolower'],
			'dummy callback method'        => [[$this, 'dummyCallback']],
		];
	}

	/**
	 * Verify that invalid callbacks are not accepted by the constructor.
	 *
	 * @dataProvider dataInvalidCallback
	 *
	 * @param mixed $input Invalid callback.
	 *
	 * @return void
	 */
	public function testInvalidCallback($input) {
		$obj = new FilteredIterator([], $input);

		$reflection = new ReflectionObject($obj);
		$property   = $reflection->getProperty('callback');
		$property->setAccessible(true);
		$callback_value = $property->getValue($obj);
		$property->setAccessible(false);

		$this->assertNull($callback_value, 'Callback property has been set to invalid callback');
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidCallback() {
		return [
			'null'                  => [null],
			'non-existent function' => ['functionname'],
			'plain object'          => [new stdClass(), 'method'],
		];
	}

	/**
	 * Dummy callback method.
	 *
	 * @return void
	 */
	public function dummyCallback() {}
}
