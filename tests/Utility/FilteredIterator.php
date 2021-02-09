<?php

class RequestsTest_Utility_FilteredIterator extends RequestsTest_TestCase {
	/**
	 * @dataProvider dataSerializeDeserializeObjects
	 */
	public function testDeserializeRequestUtilityFilteredIteratorObjects($value) {
		$serialized = serialize($value);
		if (get_class($value) === 'Requests_Utility_FilteredIterator') {
			$new_value  = unserialize($serialized);
			$reflection = new ReflectionClass('Requests_Utility_FilteredIterator');
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
			array(new Requests_Utility_FilteredIterator(array(1), 'md5')),
			array(new Requests_Utility_FilteredIterator(array(1, 2), 'sha1')),
			array(new Requests_Utility_FilteredIterator(array(1, 2, 3), 'doesnotexist')),
			array(new ArrayIterator(array(1, 2, 3))),
		);
	}
}
