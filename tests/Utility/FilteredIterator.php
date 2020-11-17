<?php

class RequestsTest_Utility_FilteredIterator extends PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider dataSerializeDeserializeObjects
	 */
	public function testDeserializeRequestUtilityFilteredIteratorObjects($value) {
		$serialized = serialize($value);
		if (get_class($value) === 'Requests_Utility_FilteredIterator') {
			$new_value = unserialize($serialized);
			if (version_compare(PHP_VERSION, '5.3', '>=')) {
				// phpcs:ignore PHPCompatibility.Syntax.NewClassMemberAccess.OnNewFound -- Wrapped in version check.
				$property = (new ReflectionClass('Requests_Utility_FilteredIterator'))->getProperty('callback');
				$property->setAccessible(true);
				$callback_value = $property->getValue($new_value);
				$this->assertSame(null, $callback_value);
			} else {
				$current_item = $new_value->current();
				$this->assertSame(null, $current_item);
			}
		} else {
			$this->assertEquals($value->count(), unserialize($serialized)->count());
		}
	}

	public function dataSerializeDeserializeObjects() {
		return array(
			array(new Requests_Utility_FilteredIterator(array(1), 'md5')),
			array(new Requests_Utility_FilteredIterator(array(1, 2), 'sha1')),
			array(new ArrayIterator(array(1, 2, 3))),
		);
	}
}
