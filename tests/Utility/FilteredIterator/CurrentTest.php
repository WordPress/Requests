<?php

namespace WpOrg\Requests\Tests\Utility\FilteredIterator;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\FilteredIterator;

/**
 * @covers \WpOrg\Requests\Utility\FilteredIterator::current
 */
final class CurrentTest extends TestCase {

	/**
	 * Tests that when there is a valid callback, the current value is always filtered.
	 *
	 * @dataProvider dataCallbackIsAppliedIfValid
	 *
	 * @param iterable $data     The array or object to be iterated on.
	 * @param callable $callback Callback to be called on each value.
	 * @param array    $expected The values expected to be seen during iteration.
	 *
	 * @return void
	 */
	public function testCallbackIsAppliedIfValid($data, $callback, $expected) {
		$iterator = new FilteredIterator($data, $callback);

		$actual = [];
		foreach ($iterator as $key => $value) {
			$actual[$key] = $value;
		}

		$this->assertSame($expected, $actual);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataCallbackIsAppliedIfValid() {
		$original = [
			'key1' => 'lowercase',
			'key2' => 'UPPER CASE',
			'key3' => 'Sentence case',
			'key4' => 'Title Case',
		];

		return [
			'invalid callback: null' => [
				'data'     => $original,
				'callback' => null,
				'expected' => $original,
			],
			'invalid callback: function does not exist' => [
				'data'     => $original,
				'callback' => 'i_am_not_callable',
				'expected' => $original,
			],
			'valid callback' => [
				'data'     => $original,
				'callback' => 'strtolower',
				'expected' => [
					'key1' => 'lowercase',
					'key2' => 'upper case',
					'key3' => 'sentence case',
					'key4' => 'title case',
				],
			],
		];
	}
}
