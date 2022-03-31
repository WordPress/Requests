<?php

namespace WpOrg\Requests\Tests\Utility\CaseInsensitiveDictionary;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\Utility\CaseInsensitiveDictionary\CaseInsensitiveDictionaryTest;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers \WpOrg\Requests\Utility\CaseInsensitiveDictionary::__construct
 */
class ConstructorTest extends TestCase {

	/**
	 * Test setting up a dictionary without entries.
	 *
	 * @return void
	 */
	public function testInitialDictionaryIsEmptyArray() {
		$dictionary = new CaseInsensitiveDictionary();

		$this->assertIsIterable($dictionary, 'Empty dictionary is not iterable');
		$this->assertCount(0, $dictionary, 'Empty dictionary has a count not equal to 0');
	}

	/**
	 * Test setting up a dictionary with entries.
	 *
	 * @return void
	 */
	public function testInitialDictionaryHasEntries() {
		$dictionary = new CaseInsensitiveDictionary(CaseInsensitiveDictionaryTest::DATASET);
		$property   = $this->getPropertyValue($dictionary, 'data');

		$this->assertIsArray($property, 'Dictionary is not an array');
		$this->assertCount(
			count(CaseInsensitiveDictionaryTest::DATASET),
			$property,
			'Entry count for initial dictionary does not match expectation'
		);
	}
}
