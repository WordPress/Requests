<?php

namespace WpOrg\Requests\Tests\Utility;

use ArrayIterator;
use ReflectionClass;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\FilteredIterator;

final class FilteredIteratorTest extends TestCase {
	/**
	 * @dataProvider dataSerializeDeserializeObjects
	 */
	public function testDeserializeRequestUtilityFilteredIteratorObjects($value) {
		$serialized = serialize($value);
		if (get_class($value) === FilteredIterator::class) {
			$new_value  = unserialize($serialized);
			$reflection = new ReflectionClass(FilteredIterator::class);
			$property   = $reflection->getProperty('callback');
			$property->setAccessible(true);
			$callback_value = $property->getValue($new_value);
			$this->assertSame(null, $callback_value);
		} else {
			$this->assertEquals($value->count(), unserialize($serialized)->count());
		}
	}

	public function dataSerializeDeserializeObjects() {
		return array(
			array(new FilteredIterator(array(1), 'md5')),
			array(new FilteredIterator(array(1, 2), 'sha1')),
			array(new FilteredIterator(array(1, 2, 3), 'doesnotexist')),
			array(new ArrayIterator(array(1, 2, 3))),
		);
	}
}
