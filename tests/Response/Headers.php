<?php
namespace Rmccue\RequestTests\Requests;

use Rmccue\Requests\Response as Response;
use Rmccue\Requests\Exception as Exception;
use PHPUnit\Framework\TestCase as TestCase;

class Headers extends TestCase {
	public function testArrayAccess() {
		$headers = new Response\Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertEquals('text/plain', $headers['Content-Type']);
	}
	public function testCaseInsensitiveArrayAccess() {
		$headers = new Response\Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertEquals('text/plain', $headers['CONTENT-TYPE']);
		$this->assertEquals('text/plain', $headers['content-type']);
	}

	/**
	 * @depends testArrayAccess
	 */
	public function testIteration() {
		$headers = new Response\Headers();
		$headers['Content-Type'] = 'text/plain';
		$headers['Content-Length'] = 10;

		foreach ($headers as $name => $value) {
			switch (strtolower($name)) {
				case 'content-type':
					$this->assertEquals('text/plain', $value);
					break;
				case 'content-length':
					$this->assertEquals(10, $value);
					break;
				default:
					throw new Exception('Invalid name: ' . $name);
			}
		}
	}

	/**
	 * @expectedException Rmccue\Requests\Exception
	 */
	public function testInvalidKey() {
		$headers = new Response\Headers();
		$headers[] = 'text/plain';
	}

	public function testMultipleHeaders() {
		$headers = new Response\Headers();
		$headers['Accept'] = 'text/html;q=1.0';
		$headers['Accept'] = '*/*;q=0.1';

		$this->assertEquals('text/html;q=1.0,*/*;q=0.1', $headers['Accept']);
	}
}