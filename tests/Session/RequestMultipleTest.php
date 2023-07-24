<?php

namespace WpOrg\Requests\Tests\Session;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Response;
use WpOrg\Requests\Session;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Session::request_multiple
 */
final class RequestMultipleTest extends TestCase {

	/**
	 * Tests receiving an exception when the request_multiple() method received an invalid input type as `$requests`.
	 *
	 * @dataProvider dataInvalidRequests
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidRequests($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($requests) must be of type array|ArrayAccess&Traversable');

		$session = new Session();
		$session->request_multiple($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidRequests() {
		$except = array_intersect(TypeProviderHelper::GROUP_ITERABLE, TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
		return TypeProviderHelper::getAllExcept($except);
	}

	/**
	 * Tests receiving an exception when the request_multiple() method received an invalid input type as `$option`.
	 *
	 * @dataProvider dataInvalidOptions
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidOptions($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($options) must be of type array');

		$session = new Session();
		$session->request_multiple([], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidOptions() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	public function testMultiple() {
		$session   = new Session($this->httpbin('/'), ['X-Requests-Session' => 'Multiple']);
		$requests  = [
			'test1' => [
				'url' => $this->httpbin('/get'),
			],
			'test2' => [
				'url' => $this->httpbin('/get'),
			],
		];
		$responses = $session->request_multiple($requests);

		// test1
		$this->assertNotEmpty($responses['test1']);
		$this->assertInstanceOf(Response::class, $responses['test1']);
		$this->assertSame(200, $responses['test1']->status_code);

		$result = json_decode($responses['test1']->body, true);
		$this->assertSame($this->httpbin('/get'), $result['url']);
		$this->assertEmpty($result['args']);

		// test2
		$this->assertNotEmpty($responses['test2']);
		$this->assertInstanceOf(Response::class, $responses['test2']);
		$this->assertSame(200, $responses['test2']->status_code);

		$result = json_decode($responses['test2']->body, true);
		$this->assertSame($this->httpbin('/get'), $result['url']);
		$this->assertEmpty($result['args']);
	}
}
