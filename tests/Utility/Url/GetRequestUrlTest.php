<?php

namespace WpOrg\Requests\Tests\Utility\Url;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\Url;

/**
 * @covers \WpOrg\Requests\Utility\Url::get_request_url
 * @covers \WpOrg\Requests\Utility\Url::build_query
 * @covers \WpOrg\Requests\Utility\Url::merge_query
 * @covers \WpOrg\Requests\Utility\Url::needs_separator
 */
final class GetRequestUrlTest extends TestCase {

	/**
	 * Test that when a $url parameter of an incorrect type gets passed, an exception gets thrown.
	 *
	 * @dataProvider dataInvalidUrlType
	 *
	 * @param mixed $input Input to pass to the function.
	 *
	 * @return void
	 */
	public function testInvalidUrlType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($url) must be of type string|Stringable');

		Url::get_request_url($input);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataInvalidUrlType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRINGABLE);
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

		Url::get_request_url('http://example.com/', $input);
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
	 * Test that an invalid URL is rejected.
	 *
	 * @return void
	 */
	public function testInvalidUrl() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid URL.');

		Url::get_request_url('/path-only', ['a' => '1']);
	}

	/**
	 * Test building the URL to request.
	 *
	 * @dataProvider dataGetRequestUrl
	 *
	 * @param string       $url      Input URL.
	 * @param array|string $data     Input data.
	 * @param string       $expected Expected function return value.
	 *
	 * @return void
	 */
	public function testGetRequestUrl($url, $data, $expected) {
		$this->assertSame($expected, Url::get_request_url($url, $data));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataGetRequestUrl() {
		return [
			// Basic behaviour.
			'no data'                                     => ['http://example.com/path?x=1', [], 'http://example.com/path?x=1'],
			'empty string data'                           => ['http://example.com/path?x=1', '', 'http://example.com/path?x=1'],
			'data, no existing query'                     => ['http://example.com/path', ['a' => '1'], 'http://example.com/path?a=1'],
			'data, existing query'                        => ['http://example.com/path?x=1', ['a' => '1'], 'http://example.com/path?x=1&a=1'],
			'nested data'                                 => ['http://example.com/', ['a' => ['b' => '1']], 'http://example.com/?a%5Bb%5D=1'],
			'null values are dropped'                     => ['http://example.com/', ['a' => '', 'b' => null], 'http://example.com/?a='],
			'data needing encoding'                       => ['http://example.com/', ['q' => 'a b+c/ü'], 'http://example.com/?q=a+b%2Bc%2F%C3%BC'],
			'string data is appended as-is'               => ['http://example.com/path?x=1', 'a=1&b=2', 'http://example.com/path?x=1&a=1&b=2'],

			// The URL is (re)built from its parts, so nothing outside the query can be changed.
			'query matching a path segment'               => ['http://example.com/x=1?x=1', ['a' => '1'], 'http://example.com/x=1?x=1&a=1'],
			'query matching the host'                     => ['http://example.com/?example.com', ['a' => '1'], 'http://example.com/?example.com&a=1'],
			'query matching the scheme'                   => ['https://example.com/?https', ['a' => '1'], 'https://example.com/?https&a=1'],
			'query repeated in the fragment'              => ['http://example.com/path?x=1#x=1', ['a' => '1'], 'http://example.com/path?x=1&a=1'],

			// Empty and "falsy" query strings are still query strings.
			'empty query string'                          => ['http://example.com/path?', ['a' => '1'], 'http://example.com/path?a=1'],
			'empty query string, no data'                 => ['http://example.com/path?', [], 'http://example.com/path?'],
			'query string "0"'                            => ['http://example.com/path?0', ['a' => '1'], 'http://example.com/path?0&a=1'],
			'query string "0", no data'                   => ['http://example.com/path?0', [], 'http://example.com/path?0'],

			// The existing query string is not altered.
			'query string with leading/trailing "&"'      => ['http://example.com/path?&x=1&', ['a' => '1'], 'http://example.com/path?&x=1&a=1'],

			// The fragment is never sent to the server.
			'fragment, no query'                          => ['http://example.com/path#frag', ['a' => '1'], 'http://example.com/path?a=1'],
			'fragment, no data'                           => ['http://example.com/path?x=1#frag', [], 'http://example.com/path?x=1'],

			// URLs without a path.
			'no path, data'                               => ['http://example.com', ['a' => '1'], 'http://example.com/?a=1'],
			'no path, query'                              => ['http://example.com?x=1', [], 'http://example.com/?x=1'],
			'no path, query and data'                     => ['http://example.com?x=1', ['a' => '1'], 'http://example.com/?x=1&a=1'],

			// Authority handling.
			'non-default port'                            => ['http://example.com:8080/path', ['a' => '1'], 'http://example.com:8080/path?a=1'],
			'default port is preserved'                   => ['http://example.com:80/path', [], 'http://example.com:80/path'],
			'userinfo'                                    => ['http://user:pass@example.com/path', ['a' => '1'], 'http://user:pass@example.com/path?a=1'],
			'IPv6 host'                                   => ['http://[::1]:8080/path', ['a' => '1'], 'http://[::1]:8080/path?a=1'],

			// The URL is not normalized, so it is sent exactly as passed (for example, a signed URL).
			'percent-encoding is preserved'               => ['http://example.com/%7euser?sig=%7eAbc%2F', ['a' => '1'], 'http://example.com/%7euser?sig=%7eAbc%2F&a=1'],
			'unencoded brackets in query are preserved'   => ['http://example.com/path?a[]=1', [], 'http://example.com/path?a[]=1'],
			'dot segments are preserved'                  => ['http://example.com/a/../b', [], 'http://example.com/a/../b'],
			'surrounding whitespace is trimmed'           => [" http://example.com/path?x=1\n", ['a' => '1'], 'http://example.com/path?x=1&a=1'],
		];
	}

	/**
	 * Test that an Iri object can be passed and is not modified.
	 *
	 * @return void
	 */
	public function testAcceptsIriWithoutModifyingIt() {
		$iri = new Iri('http://example.com/path?x=1#frag');

		$this->assertSame('http://example.com/path?x=1&a=1', Url::get_request_url($iri, ['a' => '1']), 'Returned URL does not match');
		$this->assertSame('http://example.com/path?x=1#frag', $iri->uri, 'The passed object should not be modified');
	}
}
