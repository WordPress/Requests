<?php

namespace WpOrg\Requests\Tests\Utility\CaseInsensitiveDictionary;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\Utility\CaseInsensitiveDictionary\CaseInsensitiveDictionaryTest;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers \WpOrg\Requests\Utility\CaseInsensitiveDictionary::getIterator
 */
class GetIteratorTest extends TestCase {

	/**
	 * Test iterating over a dictionary.
	 *
	 * @return void
	 */
	public function testGetIterator() {
		// Initial set up.
		$dictionary = new CaseInsensitiveDictionary(CaseInsensitiveDictionaryTest::DATASET);

		$this->assertCount(8, $dictionary, 'Dictionary is not countable');

		// If foreach() works and actually enters the loop, we're good.
		foreach ($dictionary as $key => $value) {
			$this->assertTrue(true, 'Dictionary is not iterable');
			break;
		}
	}
}
