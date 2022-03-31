<?php

namespace WpOrg\Requests\Tests\Utility\CaseInsensitiveDictionary;

use WpOrg\Requests\Tests\TestCase;
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
}
