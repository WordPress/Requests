<?php

namespace WpOrg\Requests\Tests\Utility\FilteredIterator;

use ArrayIterator;
use ReflectionClass;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\FilteredIterator;

/**
 * @covers \WpOrg\Requests\Utility\FilteredIterator::unserialize
 * @covers \WpOrg\Requests\Utility\FilteredIterator::__unserialize
 */
final class SerializationTest extends TestCase {

	/**
	 * Tests against insecure deserialization of untrusted data.
	 *
	 * @link https://github.com/WordPress/Requests/security/advisories/GHSA-52qp-jpq7-6c54
	 *
	 * @dataProvider dataSerializeDeserializeObjects
	 *
	 * @param \ArrayIterator $value Value to test with.
	 *
	 * @return void
	 */
	public function testSerializeDeserializeObjects($value) {
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
		return [
			'FilteredIterator object with one value, callback: md5' => [
				'value' => new FilteredIterator([1], 'md5'),
			],
			'FilteredIterator object with two values, callback: sha1' => [
				'value' => new FilteredIterator([1, 2], 'sha1'),
			],
			'FilteredIterator object with three values, non-existent callback' => [
				'value' => new FilteredIterator([1, 2, 3], 'doesnotexist'),
			],
			'ArrayIterator object with three values, no callback' => [
				'value' => new ArrayIterator([1, 2, 3]),
			],
		];
	}
}
