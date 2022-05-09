<?php

namespace WpOrg\Requests\Tests\Response\Headers;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\TestCase;

/**
 * @coversDefaultClass \WpOrg\Requests\Response\Headers
 */
final class GetIteratorTest extends TestCase {

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
}
