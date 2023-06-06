<?php

namespace WpOrg\Requests\Tests\Utility\CaseInsensitiveDictionary;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @coversDefaultClass \WpOrg\Requests\Utility\CaseInsensitiveDictionary
 */
class ArrayAccessTest extends TestCase {

	/**
	 * Base data set for array access tests.
	 *
	 * {@internal Note: this array and the reverse are hard-coded as using array_flip()
	 * will throw errors for invalid keys and array_combine() with array_keys() and array_values()
	 * loses a value.}
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
	 * Data to use in the data provider for the array access tests.
	 *
	 * @var array
	 */
	const DATASET_REVERSED = [
		'Uppercase key'                              => 'UPPER CASE',
		'First char in caps in key'                  => 'Proper Case',
		'Lowercase key'                              => 'lower case',
		'Null key will be converted to empty string' => null,
		'false key will become integer 0 key'        => false,
		'true key will become integer 1 key'         => true,
		'Float key will be converted to integer key (cut off)' => 5.0,
		'Explicit integer numeric key'               => 100,
	];

	/**
	 * Text string case changing functions in PHP.
	 *
	 * Used in a randomizer in the data providers.
	 *
	 * @var array
	 */
	const CHANGE_CASE_FUNCTIONS = [
		'strtolower' => true,
		'strtoupper' => true,
		'ucfirst'    => true,
		'ucwords'    => true,
	];

	/**
	 * Test trying to create an array entry without a key.
	 *
	 * @covers ::offsetSet
	 *
	 * @return void
	 */
	public function testOffsetSetWithoutKey() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Object is a dictionary, not a list');

		$dictionary   = new CaseInsensitiveDictionary();
		$dictionary[] = 'value';
	}

	/**
	 * Test array access for entries which exist.
	 *
	 * @covers ::offsetExists
	 * @covers ::offsetGet
	 * @covers ::offsetSet
	 * @covers ::offsetUnset
	 *
	 * @dataProvider dataAccessValidEntries
	 *
	 * @param mixed  $key   Item key.
	 * @param string $value Unused for this test. Item value.
	 *
	 * @return void
	 */
	public function testAccessValidEntries($key, $value) {
		// Initial set up.
		$dictionary = new CaseInsensitiveDictionary(self::DATASET);

		// Verify initial state.
		$this->assertTrue(isset($dictionary[$key]), "Key {$key} is not set");
		$this->assertSame($value, $dictionary[$key], "Value for dictionary entry for key {$key} does not match expected value");

		// Overwrite the value.
		if ($key !== null) {
			$dictionary[$key] = 'new value';
			$this->assertTrue(isset($dictionary[$key]), "Key {$key} is not set after value overwrite");
			$this->assertSame('new value', $dictionary[$key], "Value for dictionary entry for key {$key} does not match new value");
		}

		// Unset the value.
		unset($dictionary[$key]);
		$this->assertFalse(isset($dictionary[$key]), "Key {$key} is still set");
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataAccessValidEntries() {
		$data = [];

		foreach (self::DATASET_REVERSED as $key => $value) {
			if (is_string($value)) {
				$fn    = array_rand(self::CHANGE_CASE_FUNCTIONS);
				$value = $fn($value);
			}

			$data[$key] = [
				'key'   => $value,
				'value' => $key,
			];
		}

		return $data;
	}

	/**
	 * Test array access for an entry which (initially) doesn't exist.
	 *
	 * @dataProvider dataAccessInvalidEntry
	 *
	 * @covers ::offsetExists
	 * @covers ::offsetGet
	 * @covers ::offsetSet
	 * @covers ::offsetUnset
	 *
	 * @param mixed $key Key to use in the test.
	 *
	 * @return void
	 */
	public function testAccessInvalidEntry($key) {
		// Initial set up.
		$dictionary = new CaseInsensitiveDictionary(self::DATASET);

		// Verify initial state.
		$this->assertFalse(isset($dictionary[$key]), "Key {$key} is set");
		$this->assertNull($dictionary[$key], "Value for non-existent dictionary entry {$key} is not null");

		// Set the value.
		$dictionary[$key] = 'new value';
		$this->assertTrue(isset($dictionary[$key]), "Key {$key} is still not set");
		$this->assertSame('new value', $dictionary[strtoupper($key)], "Value for dictionary entry for key {$key} does not match new value");

		// Unset the value.
		unset($dictionary[$key]);
		$this->assertFalse(isset($dictionary[ucfirst($key)]), "Key {$key} is still set");
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataAccessInvalidEntry() {
		return [
			'string key'  => ['Non-existant entry'],
			'integer key' => [25],
		];
	}
}
