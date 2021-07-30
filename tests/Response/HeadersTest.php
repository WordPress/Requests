<?php

namespace Requests\Tests\Response;

use Requests\Tests\TestCase;
use Requests_Exception;
use Requests_Response_Headers;

class HeadersTest extends TestCase {
	public function testArrayAccess() {
		$headers                 = new Requests_Response_Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertSame('text/plain', $headers['Content-Type']);
	}
	public function testCaseInsensitiveArrayAccess() {
		$headers                 = new Requests_Response_Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertSame('text/plain', $headers['CONTENT-TYPE']);
		$this->assertSame('text/plain', $headers['content-type']);
	}

	/**
	 * @depends testArrayAccess
	 */
	public function testIteration() {
		$headers                   = new Requests_Response_Headers();
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
		$this->expectException(Requests_Exception::class);
		$this->expectExceptionMessage('Object is a dictionary, not a list');
		$headers   = new Requests_Response_Headers();
		$headers[] = 'text/plain';
	}

	public function testMultipleHeaders() {
		$headers           = new Requests_Response_Headers();
		$headers['Accept'] = 'text/html;q=1.0';
		$headers['Accept'] = '*/*;q=0.1';

		$this->assertSame('text/html;q=1.0,*/*;q=0.1', $headers['Accept']);
	}
}
