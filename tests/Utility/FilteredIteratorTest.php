<?php

namespace WpOrg\Requests\Tests\Utility;

use ArrayIterator;
use ReflectionClass;
use ReflectionObject;
use stdClass;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\FilteredIterator;

/**
 * @coversDefaultClass \WpOrg\Requests\Utility\FilteredIterator
 */
final class FilteredIteratorTest extends TestCase {

	/**
	 * Tests against insecure deserialization of untrusted data.
	 *
	 * @link https://github.com/WordPress/Requests/security/advisories/GHSA-52qp-jpq7-6c54
	 *
	 * @covers ::unserialize
	 * @covers ::__unserialize
	 * @covers ::__wakeup
	 *
	 * @dataProvider dataSerializeDeserializeObjects
	 *
	 * @param \ArrayIterator $value Value to test with.
	 *
	 * @return void
	 */
	public function testDeserializeRequestUtilityFilteredIteratorObjects($value) {
		$serialized = serialize($value);
		if (get_class($value) === FilteredIterator::class) {
			$new_value  = unserialize($serialized);
			$reflection = new ReflectionClass(FilteredIterator::class);
			$property   = $reflection->getProperty('callback');
			$property->setAccessible(true);
			$callback_value = $property->getValue($new_value);
			$this->assertNull($callback_value, 'Callback is not null');
		} else {
			$this->assertSame(
				$value->count(),
				unserialize($serialized)->count(),
				'Unserialized count is not equivalent'
			);
		}
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataSerializeDeserializeObjects() {
		return array(
			'FilteredIterator object with one value, callback: md5' => array(
				'value' => new FilteredIterator(array(1), 'md5'),
			),
			'FilteredIterator object with two values, callback: sha1' => array(
				'value' => new FilteredIterator(array(1, 2), 'sha1'),
			),
			'FilteredIterator object with three values, non-existent callback' => array(
				'value' => new FilteredIterator(array(1, 2, 3), 'doesnotexist'),
			),
			'ArrayIterator object with three values, no callback' => array(
				'value' => new ArrayIterator(array(1, 2, 3)),
			),
		);
	}

	/**
	 * Tests that valid $data is accepted by the constructor.
	 *
	 * @dataProvider dataConstructorValidData
	 *
	 * @covers ::__construct
	 *
	 * @param mixed $input Valid input.
	 *
	 * @return void
	 */
	public function testConstructorValidData($input) {
		$this->assertInstanceOf(FilteredIterator::class, new FilteredIterator($input, 'ltrim'));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataConstructorValidData() {
		return array(
			'array'           => array(array(1, 2, 3)),
			'iterable object' => array(new ArrayIterator(array(1, 2, 3))),
		);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as `$data` to the constructor.
	 *
	 * @dataProvider dataConstructorInvalidData
	 *
	 * @covers ::__construct
	 *
	 * @param mixed $input Invalid input.
	 *
	 * @return void
	 */
	public function testConstructorInvalidData($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($data) must be of type iterable');

		new FilteredIterator($input, 'ltrim');
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataConstructorInvalidData() {
		return array(
			'null'              => array(null),
			'float'             => array(1.1),
			'stringable object' => array(new StringableObject('value')),
		);
	}

	/**
	 * Tests that valid $callback is accepted by the constructor.
	 *
	 * @dataProvider dataConstructorValidCallback
	 *
	 * @covers ::__construct
	 *
	 * @param mixed $input Valid input.
	 *
	 * @return void
	 */
	public function testConstructorValidCallback($input) {
		$obj = new FilteredIterator(array(), $input);

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
	public function dataConstructorValidCallback() {
		return array(
			'existing PHP native function' => array('strtolower'),
			'dummy callback method'        => array(array($this, 'dummyCallback')),
		);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as `$callback` to the constructor.
	 *
	 * @dataProvider dataConstructorInvalidCallback
	 *
	 * @covers ::__construct
	 *
	 * @param mixed $input Invalid callback.
	 *
	 * @return void
	 */
	public function testConstructorInvalidCallback($input) {
		$obj = new FilteredIterator(array(), $input);

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
	public function dataConstructorInvalidCallback() {
		return array(
			'null'                  => array(null),
			'non-existent function' => array('functionname'),
			'plain object'          => array(new stdClass(), 'method'),
		);
	}

	/**
	 * Dummy callback method.
	 *
	 * @return void
	 */
	public function dummyCallback() {}
}
