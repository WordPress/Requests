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
	 * Test retrieving the request-target for a URL.
	 *
	 * @dataProvider dataGetRequestTarget
	 *
	 * @param string $url      Input URL.
	 * @param string $expected Expected function return value.
	 *
	 * @return void
	 */
	public function testGetRequestTarget($url, $expected) {
		$this->assertSame($expected, Url::get_request_target($url));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataGetRequestTarget() {
		return [
			'no path'                      => ['http://example.com', '/'],
			'no path, query'               => ['http://example.com?x=1', '/?x=1'],
			'path'                         => ['http://example.com/path/to', '/path/to'],
			'path and query'               => ['http://example.com/path?x=1&a=1', '/path?x=1&a=1'],
			'query matching the path'      => ['http://example.com/x=1?x=1', '/x=1?x=1'],
			'empty query string'           => ['http://example.com/path?', '/path?'],
			'query string "0"'             => ['http://example.com/path?0', '/path?0'],
			'fragment is never sent'       => ['http://example.com/path?x=1#frag', '/path?x=1'],
			'authority is not included'    => ['https://user:pass@example.com:8443/path', '/path'],
			'dot segments are resolved'    => ['http://example.com/a/./b/../c', '/a/c'],
			'non-ASCII characters encoded' => ['http://example.com/ü?q=ä', '/%C3%BC?q=%C3%A4'],
			'spaces encoded'               => ['http://example.com/a b?q=a b', '/a%20b?q=a%20b'],
			'encoded slash preserved'      => ['http://example.com/a%2Fb?x=%2F', '/a%2Fb?x=%2F'],
		];
	}
}
