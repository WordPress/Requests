<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\TestCase;

final class HeadersTest extends TestCase {

	/**
	 * Test receiving an Exception when no key is provided when setting an entry.
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
		return array(
			'access using case as set' => array('Content-Type'),
			'access using lowercase'   => array('content-type'),
			'access using uppercase'   => array('CONTENT-TYPE'),
		);
	}

	/**
	 * Test that when multiple headers are set using the same key, requesting the key will return the
	 * combined values flattened into a single, comma-separated string.
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
	 * Test that null is returned when a non-registered header is requested.
	 *
	 * @return void
	 */
	public function testOffsetGetReturnsNullForNonRegisteredHeader() {
		$headers                 = new Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertNull($headers['not-content-type']);
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
	public function dataGetValues() {
		return array(
			'using case as set, single entry header' => array(
				'key'      => 'Content-Type',
				'expected' => array(
					'text/plain',
				),
			),
			'using lowercase, single entry header' => array(
				'key'      => 'content-length',
				'expected' => array(
					10,
				),
			),
			'using uppercase, multiple entry header' => array(
				'key'      => 'ACCEPT',
				'expected' => array(
					'text/html;q=1.0',
					'*/*;q=0.1',
				),
			),
			'non-registered string key' => array(
				'key'      => 'my-custom-header',
				'expected' => null,
			),
			'non-registered integer key' => array(
				'key'      => 10,
				'expected' => null,
			),
		);
	}

	/**
	 * Test iterator access for the object is supported.
	 *
	 * Includes making sure that:
	 * - keys are handled case-insensitively.
	 * - multiple keys with the same name are flattened into one value.
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
}
