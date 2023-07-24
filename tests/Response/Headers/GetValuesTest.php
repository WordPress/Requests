<?php

namespace WpOrg\Requests\Tests\Response\Headers;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Response\Headers::getValues
 */
final class GetValuesTest extends TestCase {

	/**
	 * Tests receiving an exception when an invalid offset is passed to getValues().
	 *
	 * @dataProvider dataInvalidOffset
	 *
	 * @param mixed $key Requested offset.
	 *
	 * @return void
	 */
	public function testInvalidOffset($key) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($offset) must be of type string|int');

		$headers = new Headers();
		$headers->getValues($key);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidOffset() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING, TypeProviderHelper::GROUP_INT);
	}

	/**
	 * Test retrieving all values for a given header (case-insensitively).
	 *
	 * @dataProvider dataGetValues
	 *
	 * @param string      $key      Key to request.
	 * @param string|null $expected Expected return value.
	 *
	 * @return void
	 */
	public function testGetValues($key, $expected) {
		$headers                   = new Headers();
		$headers['Content-Type']   = 'text/plain';
		$headers['Content-Length'] = 10;
		$headers['Accept']         = 'text/html;q=1.0';
		$headers['Accept']         = '*/*;q=0.1';

		$this->assertSame($expected, $headers->getValues($key));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataGetValues() {
		return [
			'using case as set, single entry header' => [
				'key'      => 'Content-Type',
				'expected' => [
					'text/plain',
				],
			],
			'using lowercase, single entry header' => [
				'key'      => 'content-length',
				'expected' => [
					10,
				],
			],
			'using uppercase, multiple entry header' => [
				'key'      => 'ACCEPT',
				'expected' => [
					'text/html;q=1.0',
					'*/*;q=0.1',
				],
			],
			'non-registered string key' => [
				'key'      => 'my-custom-header',
				'expected' => null,
			],
			'non-registered integer key' => [
				'key'      => 10,
				'expected' => null,
			],
		];
	}
}
