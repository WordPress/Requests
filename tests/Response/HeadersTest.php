<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\TestCase;

class HeadersTest extends TestCase {
	public function testArrayAccess() {
		$headers                 = new Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertSame('text/plain', $headers['Content-Type']);
	}
	public function testCaseInsensitiveArrayAccess() {
		$headers                 = new Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertSame('text/plain', $headers['CONTENT-TYPE']);
		$this->assertSame('text/plain', $headers['content-type']);
	}

	/**
	 * @depends testArrayAccess
	 */
	public function testIteration() {
		$headers                   = new Headers();
		$headers['Content-Type']   = 'text/plain';
		$headers['Content-Length'] = 10;

		foreach ($headers as $name => $value) {
			switch (strtolower($name)) {
				case 'content-type':
					$this->assertSame('text/plain', $value);
					break;
				case 'content-length':
					$this->assertSame('10', $value);
					break;
				default:
					throw new Exception('Invalid name: ' . $name);
			}
		}
	}

	public function testInvalidKey() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Object is a dictionary, not a list');
		$headers   = new Headers();
		$headers[] = 'text/plain';
	}

	public function testMultipleHeaders() {
		$headers           = new Headers();
		$headers['Accept'] = 'text/html;q=1.0';
		$headers['Accept'] = '*/*;q=0.1';

		$this->assertSame('text/html;q=1.0,*/*;q=0.1', $headers['Accept']);
	}
}
