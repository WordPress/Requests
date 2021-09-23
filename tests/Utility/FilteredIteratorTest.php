<?php

namespace WpOrg\Requests\Tests\Utility;

use ArrayIterator;
use ReflectionClass;
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
}
