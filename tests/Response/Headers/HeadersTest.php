<?php

namespace WpOrg\Requests\Tests\Response\Headers;

use stdClass;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\TestCase;

/**
 * @coversDefaultClass \WpOrg\Requests\Response\Headers
 */
final class HeadersTest extends TestCase {

	/**
	 * Test receiving an Exception when no key is provided when setting an entry.
	 *
	 * @covers ::offsetSet
	 *
	 * @return void
	 */
	public function testOffsetSetInvalidKey() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Object is a dictionary, not a list');

		$headers   = new Headers();
		$headers[] = 'text/plain';
	}

	/**
	 * Test array access for the object is supported and supported in a case-insensitive manner.
	 *
	 * @covers ::offsetSet
	 * @covers ::offsetGet
	 *
	 * @dataProvider dataCaseInsensitiveArrayAccess
	 *
	 * @param string $key Key to request.
	 *
	 * @return void
	 */
	public function testCaseInsensitiveArrayAccess($key) {
		$headers                 = new Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertSame('text/plain', $headers[$key]);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataCaseInsensitiveArrayAccess() {
		return [
			'access using case as set' => ['Content-Type'],
			'access using lowercase'   => ['content-type'],
			'access using uppercase'   => ['CONTENT-TYPE'],
		];
	}

	/**
	 * Test that when multiple headers are set using the same key, requesting the key will return the
	 * combined values flattened into a single, comma-separated string.
	 *
	 * @covers ::offsetSet
	 * @covers ::offsetGet
	 * @covers ::flatten
	 *
	 * @return void
	 */
	public function testMultipleHeaders() {
		$headers           = new Headers();
		$headers['Accept'] = 'text/html;q=1.0';
		$headers['Accept'] = '*/*;q=0.1';

		$this->assertSame('text/html;q=1.0,*/*;q=0.1', $headers['Accept']);
	}

	/**
	 * Test that non-string array keys are handled correctly.
	 *
	 * @covers ::offsetSet
	 *
	 * @dataProvider dataOffsetSetDoesNotTryToLowercaseNonStringKeys
	 *
	 * @param mixed           $key         Key to set.
	 * @param string|int|null $request_key Key to retrieve if different.
	 *
	 * @return void
	 */
	public function testOffsetSetDoesNotTryToLowercaseNonStringKeys($key, $request_key = null) {
		$headers       = new Headers();
		$headers[$key] = 'value';

		if (!isset($request_key)) {
			$request_key = $key;
		}

		$this->assertSame('value', $headers[$request_key]);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataOffsetSetDoesNotTryToLowercaseNonStringKeys() {
		return [
			'integer key'       => [10],
			'boolean false key' => [false, 0],
		];
	}

	/**
	 * Test that multiple headers can be registered on a non-string key.
	 *
	 * @covers ::offsetGet
	 * @covers ::offsetSet
	 *
	 * @return void
	 */
	public function testOffsetSetRegisterMultipleHeadersOnIntegerKey() {
		$headers     = new Headers();
		$headers[10] = 'value1';
		$headers[10] = 'value2';

		$this->assertSame('value1,value2', $headers[10]);
	}

	/**
	 * Test that null is returned when a non-registered header is requested.
	 *
	 * @covers ::offsetGet
	 *
	 * @dataProvider dataOffsetGetReturnsNullForNonRegisteredHeader
	 *
	 * @param mixed $key Key to request.
	 *
	 * @return void
	 */
	public function testOffsetGetReturnsNullForNonRegisteredHeader($key) {
		$headers                 = new Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertNull($headers[$key]);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataOffsetGetReturnsNullForNonRegisteredHeader() {
		return [
			// This test case also tests that no "passing null to non-nullable" deprecation is thrown in PHP 8.1.
			'null'                       => [null],
			'non-registered integer key' => [10],
			'non-registred string key'   => ['not-content-type'],
		];
	}

	/**
	 * Test retrieving all values for a given header (case-insensitively).
	 *
	 * @covers ::getValues
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
	public function dataGetValues() {
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

	/**
	 * Tests receiving an exception when an invalid offset is passed to getValues().
	 *
	 * @covers ::getValues
	 *
	 * @dataProvider dataGetValuesInvalidOffset
	 *
	 * @param mixed $key Requested offset.
	 *
	 * @return void
	 */
	public function testGetValuesInvalidOffset($key) {
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
	public function dataGetValuesInvalidOffset() {
		return [
			'null'          => [null],
			'boolean false' => [false],
		];
	}

	/**
	 * Test iterator access for the object is supported.
	 *
	 * Includes making sure that:
	 * - keys are handled case-insensitively.
	 * - multiple keys with the same name are flattened into one value.
	 *
	 * @covers ::getIterator
	 * @covers ::flatten
	 *
	 * @return void
	 */
	public function testIteration() {
		$headers                   = new Headers();
		$headers['Content-Type']   = 'text/plain';
		$headers['Content-Length'] = 10;
		$headers['Accept']         = 'text/html;q=1.0';
		$headers['Accept']         = '*/*;q=0.1';

		foreach ($headers as $name => $value) {
			switch (strtolower($name)) {
				case 'accept':
					$this->assertSame('text/html;q=1.0,*/*;q=0.1', $value, 'Accept header does not match');
					break;
				case 'content-type':
					$this->assertSame('text/plain', $value, 'Content-Type header does not match');
					break;
				case 'content-length':
					$this->assertSame('10', $value, 'Content-Length header does not match');
					break;
				default:
					throw new Exception('Invalid offset key: ' . $name);
			}
		}
	}

	/**
	 * Tests flattening of data.
	 *
	 * @covers ::flatten
	 *
	 * @dataProvider dataFlatten
	 *
	 * @param string|array $input    Value to flatten.
	 * @param string       $expected Expected output value.
	 *
	 * @return void
	 */
	public function testFlatten($input, $expected) {
		$headers = new Headers();
		$this->assertSame($expected, $headers->flatten($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataFlatten() {
		return [
			'string'            => ['text', 'text'],
			'empty array'       => [[], ''],
			'array with values' => [['text', 10, 'more text'], 'text,10,more text'],
		];
	}

	/**
	 * Tests receiving an exception when an invalid value is passed to flatten().
	 *
	 * @covers ::flatten
	 *
	 * @dataProvider dataFlattenInvalidValue
	 *
	 * @param mixed $input Value to flatten.
	 *
	 * @return void
	 */
	public function testFlattenInvalidValue($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($value) must be of type string|array');

		$headers = new Headers();
		$headers->flatten($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataFlattenInvalidValue() {
		return [
			'null'          => [null],
			'boolean false' => [false],
			'plain object'  => [new stdClass()],
		];
	}
}
