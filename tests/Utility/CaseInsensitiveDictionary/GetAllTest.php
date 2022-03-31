<?php

namespace WpOrg\Requests\Tests\Utility\CaseInsensitiveDictionary;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers \WpOrg\Requests\Utility\CaseInsensitiveDictionary::getAll
 */
class GetAllTest extends TestCase {

	/**
	 * Base data set for array access tests.
	 *
	 * @var array
	 */
	const DATASET = [
		'UPPER CASE'  => 'Uppercase key',
		'Proper Case' => 'First char in caps in key',
		'lower case'  => 'Lowercase key',
		null          => 'Null key will be converted to empty string',
		false         => 'false key will become integer 0 key',
		true          => 'true key will become integer 1 key',
		5.0           => 'Float key will be converted to integer key (cut off)',
		100           => 'Explicit integer numeric key',
	];

	/**
	 * Test retrieving all data as recorded in the dictionary.
	 *
	 * Take note of the key changes!
	 *
	 * @return void
	 */
	public function testGetAll() {
		$expected = [
			'upper case'  => 'Uppercase key',
			'proper case' => 'First char in caps in key',
			'lower case'  => 'Lowercase key',
			''            => 'Null key will be converted to empty string',
			0             => 'false key will become integer 0 key',
			1             => 'true key will become integer 1 key',
			5             => 'Float key will be converted to integer key (cut off)',
			100           => 'Explicit integer numeric key',
		];

		$dictionary = new CaseInsensitiveDictionary(self::DATASET);
		$this->assertSame($expected, $dictionary->getAll());
	}
}
