<?php

namespace WpOrg\Requests\Tests\Utility\Url;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\Url;

/**
 * @covers \WpOrg\Requests\Utility\Url::get_request_target
 */
final class GetRequestTargetTest extends TestCase {

	/**
	 * Test that when a $url parameter of an incorrect type gets passed, an exception gets thrown.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $input Input to pass to the function.
	 *
	 * @return void
	 */
	public function testInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($url) must be of type string|Stringable');

		Url::get_request_target($input);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataInvalidInputType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Test that an invalid URL is rejected.
	 *
	 * @return void
	 */
	public function testInvalidUrl() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid URL.');

		Url::get_request_target('example.com/path');
	}

	/**
	 * Test that when a $data parameter of an incorrect type gets passed, an exception gets thrown.
	 *
	 * @dataProvider dataInvalidDataType
	 *
	 * @param mixed $input Input to pass to the function.
	 *
	 * @return void
	 */
	public function testInvalidDataType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($data) must be of type array|string');

		Url::get_request_target('http://example.com/', $input);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataInvalidDataType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY, TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Test retrieving the request-target for a URL.
	 *
	 * @dataProvider dataGetRequestTarget
	 *
	 * @param string       $url      Input URL.
	 * @param array|string $data     Input data.
	 * @param string       $expected Expected function return value.
	 *
	 * @return void
	 */
	public function testGetRequestTarget($url, $data, $expected) {
		$this->assertSame($expected, Url::get_request_target($url, $data));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataGetRequestTarget() {
		return [
			'no path'                          => ['http://example.com', [], '/'],
			'no path, query'                   => ['http://example.com?x=1', [], '/?x=1'],
			'no path, data'                    => ['http://example.com', ['a' => '1'], '/?a=1'],
			'no path, query and data'          => ['http://example.com?x=1', ['a' => '1'], '/?x=1&a=1'],
			'path'                             => ['http://example.com/path/to', [], '/path/to'],
			'path, query and data'             => ['http://example.com/path?x=1', ['a' => '1'], '/path?x=1&a=1'],
			'string data'                      => ['http://example.com/path', 'a=1&b=2', '/path?a=1&b=2'],
			'query matching the path'          => ['http://example.com/x=1?x=1', ['a' => '1'], '/x=1?x=1&a=1'],
			'empty query string'               => ['http://example.com/path?', [], '/path?'],
			'empty query string, data'         => ['http://example.com/path?', ['a' => '1'], '/path?a=1'],
			'query string "0"'                 => ['http://example.com/path?0', ['a' => '1'], '/path?0&a=1'],
			'fragment is never sent'           => ['http://example.com/path?x=1#frag', ['a' => '1'], '/path?x=1&a=1'],
			'authority is not included'        => ['https://user:pass@example.com:8443/path', [], '/path'],
			'IPv6 host is not included'        => ['http://[::1]:8080/path?x=1', [], '/path?x=1'],
			'percent-encoding is preserved'    => ['http://example.com/%7euser?sig=%7eAbc%2F', [], '/%7euser?sig=%7eAbc%2F'],
			'unencoded brackets are preserved' => ['http://example.com/path?a[]=1', [], '/path?a[]=1'],
			'dot segments are preserved'       => ['http://example.com/a/./b/../c', [], '/a/./b/../c'],
		];
	}
}
