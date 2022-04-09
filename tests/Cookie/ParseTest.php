<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @coversDefaultClass \WpOrg\Requests\Cookie
 */
final class ParseTest extends TestCase {

	/**
	 * Tests receiving an exception when the parse() method received an invalid input type as `$cookie_header`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @covers ::parse
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testParseInvalidCookieHeader($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($cookie_header) must be of type string');

		Cookie::parse($input);
	}

	/**
	 * Tests receiving an exception when the parse() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @covers ::parse
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testParseInvalidName($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($name) must be of type string');

		Cookie::parse('test', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidStringInput() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Tests receiving an exception when the parse() method received an invalid input type as `$reference_time`.
	 *
	 * @covers ::parse
	 *
	 * @return void
	 */
	public function testParseInvalidReferenceTime() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #5 ($reference_time) must be of type integer|null');

		Cookie::parse('test', 'test', 'now');
	}

	/**
	 * Tests receiving an exception when the parse_from_headers() method received an invalid input type as `$reference_time`.
	 *
	 * @covers ::parse_from_headers
	 *
	 * @return void
	 */
	public function testParseFromHeadersInvalidReferenceTime() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #5 ($reference_time) must be of type integer|null');

		$origin                = new Iri();
		$headers               = new Headers();
		$headers['Set-Cookie'] = 'name=value;';

		Cookie::parse_from_headers($headers, $origin, 'now');
	}

	/**
	 * Document how the parse() method handles basic input parsing in combination with
	 * whether a predefined cookie name was passed or not.
	 *
	 * @dataProvider dataBasicNameValueParsing
	 *
	 * @covers ::parse
	 *
	 * @param string $header   Cookie header string.
	 * @param string $name     Name to be passsed.
	 * @param array  $expected Array with expectations to be verified via check_parsed_cookie().
	 *                         Keys which can be set to be verified: 'name', 'value', 'expired'.
	 *
	 * @return void
	 */
	public function testBasicNameValueParsing($header, $name, $expected) {
		$cookie = Cookie::parse($header, $name);

		$this->check_parsed_cookie($cookie, $expected);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataBasicNameValueParsing() {
		return [
			'Header value only, no name: expect empty name, value contains cookie info' => [
				'header'   => 'bar',
				'name'     => '',
				'expected' => ['name' => '', 'value' => 'bar'],
			],
			'Header key=value, no name: expect cookie key set as name' => [
				'header'   => 'foo=bar',
				'name'     => '',
				'expected' => ['name' => 'foo', 'value' => 'bar'],
			],
			'Header value only, explicit name: expect passed name set as name, value contains cookie info' => [
				'header'   => 'bar',
				'name'     => 'passed-name',
				'expected' => ['name' => 'passed-name', 'value' => 'bar'],
			],
			'Header key=value, explicit name: expect passed name set as name, cookie key=value set as value' => [
				'header'   => 'foo=bar',
				'name'     => 'passed-name',
				'expected' => ['name' => 'passed-name', 'value' => 'foo=bar'],
			],

			// Safeguard whitespace handling.
			'Surrounding whitespace should be stripped off name and value [1]' => [
				'header'   => '  bar  ',
				'name'     => '  passed-name  ',
				'expected' => ['name' => 'passed-name', 'value' => 'bar'],
			],
			'Surrounding whitespace should be stripped off name and value [2]' => [
				'header'   => '   foo   =   bar   ',
				'name'     => '',
				'expected' => ['name' => 'foo', 'value' => 'bar'],
			],
		];
	}

	/**
	 * Verify the parsing of a cookie header string into a cookie.
	 *
	 * @dataProvider dataParseResult
	 *
	 * @param string $header              Cookie header string.
	 * @param array  $expected            Array with expectations to be verified via check_parsed_cookie().
	 *                                    Keys which can be set to be verified: 'name', 'value', 'expired'.
	 * @param array  $expected_attributes Attribute expectations to verify.
	 *
	 * @return void
	 */
	public function testParsingHeader($header, $expected, $expected_attributes = []) {
		// Set the reference time to 2014-01-01 00:00:00.
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2014);

		$cookie = Cookie::parse($header, '', $reference_time);
		$this->check_parsed_cookie($cookie, $expected, $expected_attributes);
	}

	/**
	 * Double-normalizes the cookie data to ensure we catch any issues there.
	 *
	 * @dataProvider dataParseResult
	 *
	 * @param string $header              Cookie header string.
	 * @param array  $expected            Array with expectations to be verified via check_parsed_cookie().
	 *                                    Keys which can be set to be verified: 'name', 'value', 'expired'.
	 * @param array  $expected_attributes Attribute expectations to verify.
	 *
	 * @return void
	 */
	public function testParsingHeaderDouble($header, $expected, $expected_attributes = []) {
		// Set the reference time to 2014-01-01 00:00:00.
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2014);

		$cookie = Cookie::parse($header, '', $reference_time);

		// Normalize the value again.
		$cookie->normalize();

		$this->check_parsed_cookie($cookie, $expected, $expected_attributes);
	}

	/**
	 * Verify parsing of cookies in Header objects.
	 *
	 * @dataProvider dataParseResult
	 *
	 * @param string $header              Cookie header string.
	 * @param array  $expected            Array with expectations to be verified via check_parsed_cookie().
	 *                                    Keys which can be set to be verified: 'name', 'value', 'expired'.
	 * @param array  $expected_attributes Attribute expectations to verify.
	 *
	 * @return void
	 */
	public function testParsingHeaderObject($header, $expected, $expected_attributes = []) {
		$headers               = new Headers();
		$headers['Set-Cookie'] = $header;

		// Set the reference time to 2014-01-01 00:00:00.
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2014);

		$parsed = Cookie::parse_from_headers($headers, null, $reference_time);
		$this->assertCount(1, $parsed);

		$cookie = reset($parsed);
		$this->check_parsed_cookie($cookie, $expected, $expected_attributes);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataParseResult() {
		return [
			/*
			 * Expiration date parsing.
			 */
			// RFC 822, updated by RFC 1123.
			'Expiration date parsing: format: RFC 822, updated by RFC 1123; expired date' => [
				'header'              => 'foo=bar; Expires=Thu, 5-Dec-2013 04:50:12 GMT',
				'expected'            => ['expired' => true],
				'expected_attributes' => ['expires' => gmmktime(4, 50, 12, 12, 5, 2013)],
			],
			'Expiration date parsing: format: RFC 822, updated by RFC 1123; non-expired date' => [
				'header'              => 'foo=bar; Expires=Fri, 5-Dec-2014 04:50:12 GMT',
				'expected'            => ['expired' => false],
				'expected_attributes' => ['expires' => gmmktime(4, 50, 12, 12, 5, 2014)],
			],
			// RFC 850, obsoleted by RFC 1036.
			'Expiration date parsing: format: RFC 850, obsoleted by RFC 1036; expired date' => [
				'header'              => 'foo=bar; Expires=Thursday, 5-Dec-2013 04:50:12 GMT',
				'expected'            => ['expired' => true],
				'expected_attributes' => ['expires' => gmmktime(4, 50, 12, 12, 5, 2013)],
			],
			'Expiration date parsing: format: RFC 850, obsoleted by RFC 1036; non-expired date' => [
				'header'              => 'foo=bar; Expires=Friday, 5-Dec-2014 04:50:12 GMT',
				'expected'            => ['expired' => false],
				'expected_attributes' => ['expires' => gmmktime(4, 50, 12, 12, 5, 2014)],
			],
			// Test with asctime().
			'Expiration date parsing: format: asctime(); expired date' => [
				'header'              => 'foo=bar; Expires=Thu Dec  5 04:50:12 2013',
				'expected'            => ['expired' => true],
				'expected_attributes' => ['expires' => gmmktime(4, 50, 12, 12, 5, 2013)],
			],
			'Expiration date parsing: format: asctime(); non-expired date' => [
				'header'              => 'foo=bar; Expires=Fri Dec  5 04:50:12 2014',
				'expected'            => ['expired' => false],
				'expected_attributes' => ['expires' => gmmktime(4, 50, 12, 12, 5, 2014)],
			],
			// Invalid.
			'Expiration date parsing: invalid expiration date' => [
				'header'              => 'foo=bar; Expires=never',
				'expected'            => [],
				'expected_attributes' => ['expires' => null],
			],

			/*
			 * Max-Age parsing.
			 */
			'Max-Age parsing: value: 10' => [
				'header'              => 'foo=bar; Max-Age=10',
				'expected'            => ['expired' => false],
				'expected_attributes' => ['max-age' => gmmktime(0, 0, 10, 1, 1, 2014)],
			],
			'Max-Age parsing: value: 3660' => [
				'header'              => 'foo=bar; Max-Age=3660',
				'expected'            => ['expired' => false],
				'expected_attributes' => ['max-age' => gmmktime(1, 1, 0, 1, 1, 2014)],
			],
			'Max-Age parsing: value: 0' => [
				'header'              => 'foo=bar; Max-Age=0',
				'expected'            => ['expired' => true],
				'expected_attributes' => ['max-age' => 0],
			],
			'Max-Age parsing: value: 1000' => [
				'header'              => 'foo=bar; Max-Age=-1000',
				'expected'            => ['expired' => true],
				'expected_attributes' => ['max-age' => 0],
			],
			// Invalid (non-digit character).
			'Max-Age parsing: value: 1e6 (non-digit)' => [
				'header'              => 'foo=bar; Max-Age=1e6',
				'expected'            => ['expired' => false],
				'expected_attributes' => ['max-age' => null],
			],

			/*
			 * Arbitrary attributes.
			 */
			'Attribute parsing where the attribute does not contain an = sign' => [
				'header'              => 'foo=bar; HttpOnly',
				'expected'            => [],
				'expected_attributes' => ['httponly' => true],
			],
			'Attribute parsing: surrounding whitespace should be stripped' => [
				'header'              => 'foo=bar; ArbitraryName   =  some-value   ',
				'expected'            => [],
				'expected_attributes' => ['ArbitraryName' => 'some-value'],
			],

			/*
			 * Altogether now: multiple attributes in one cookie header.
			 */
			'Parsing when there are multiple attributes' => [
				'header'              => 'foo=bar; Expires=Fri, 5-Dec-2014 04:50:12 GMT; Max-Age=3660; ArbitraryName   =  some-value   ',
				'expected'            => ['expired' => false],
				'expected_attributes' => [
					'expires'       => gmmktime(4, 50, 12, 12, 5, 2014),
					'max-age'       => gmmktime(1, 1, 0, 1, 1, 2014),
					'ArbitraryName' => 'some-value',
				],
			],
		];
	}

	/**
	 * @dataProvider dataParsingHeaderWithOrigin
	 */
	public function testParsingHeaderWithOrigin($header, $origin, $expected, $expected_attributes = [], $expected_flags = []) {
		$origin                = new Iri($origin);
		$headers               = new Headers();
		$headers['Set-Cookie'] = $header;

		// Set the reference time to 2014-01-01 00:00:00
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2014);

		$parsed = Cookie::parse_from_headers($headers, $origin, $reference_time);
		if (isset($expected['invalid'])) {
			$this->assertCount(0, $parsed);
			return;
		}

		$this->assertCount(1, $parsed);

		$cookie = reset($parsed);
		$this->check_parsed_cookie($cookie, $expected, $expected_attributes, $expected_flags);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataParsingHeaderWithOrigin() {
		return [
			# Varying origin path
			[
				'name=value',
				'http://example.com/',
				[],
				['path' => '/'],
				['host-only' => true],
			],
			[
				'name=value',
				'http://example.com/test',
				[],
				['path' => '/'],
				['host-only' => true],
			],
			[
				'name=value',
				'http://example.com/test/',
				[],
				['path' => '/test'],
				['host-only' => true],
			],
			[
				'name=value',
				'http://example.com/test/abc',
				[],
				['path' => '/test'],
				['host-only' => true],
			],
			[
				'name=value',
				'http://example.com/test/abc/',
				[],
				['path' => '/test/abc'],
				['host-only' => true],
			],

			# With specified path
			[
				'name=value; path=/',
				'http://example.com/',
				[],
				['path' => '/'],
				['host-only' => true],
			],
			[
				'name=value; path=/test',
				'http://example.com/',
				[],
				['path' => '/test'],
				['host-only' => true],
			],
			[
				'name=value; path=/test/',
				'http://example.com/',
				[],
				['path' => '/test/'],
				['host-only' => true],
			],

			# Invalid path
			[
				'name=value; path=yolo',
				'http://example.com/',
				[],
				['path' => '/'],
				['host-only' => true],
			],
			[
				'name=value; path=yolo',
				'http://example.com/test/',
				[],
				['path' => '/test'],
				['host-only' => true],
			],

			# Cross-origin cookies, reject!
			[
				'name=value; domain=example.org',
				'http://example.com/',
				['invalid' => false],
			],

			# Empty Domain
			[
				'name=value; domain=',
				'http://example.com/test/',
				[],
			],

			# Subdomain cookies
			[
				'name=value; domain=test.example.com',
				'http://test.example.com/',
				[],
				['domain' => 'test.example.com'],
				['host-only' => false],
			],
			[
				'name=value; domain=example.com',
				'http://test.example.com/',
				[],
				['domain' => 'example.com'],
				['host-only' => false],
			],
		];
	}

	/**
	 * Test helper function.
	 *
	 * @param \WpOrg\Requests\Cookie $cookie              Cookie object.
	 * @param array                  $expected            Array with expectations to be verified via check_parsed_cookie().
	 *                                                    Keys which can be set to be verified: 'name', 'value', 'expired'.
	 * @param array                  $expected_attributes Array with attribute expectations.
	 * @param array                  $expected_flags      Array with flag expectations.
	 *
	 * @return void
	 */
	private function check_parsed_cookie($cookie, $expected, $expected_attributes = [], $expected_flags = []) {
		$this->assertInstanceof(Cookie::class, $cookie, 'Parsing did not yield a Cookie object');

		if (isset($expected['name'])) {
			$this->assertSame($expected['name'], $cookie->name, 'Cookie name does not match expectation');
		}

		if (isset($expected['value'])) {
			$this->assertSame($expected['value'], $cookie->value, 'Cookie value does not match expectation');
		}

		if (isset($expected['expired'])) {
			$this->assertSame($expected['expired'], $cookie->is_expired(), 'Cookie expiration identification does not match expectation');
		}

		if (isset($expected_attributes) && !empty($expected_attributes)) {
			foreach ($expected_attributes as $attr_key => $attr_val) {
				$this->assertSame($attr_val, $cookie->attributes[$attr_key], "Attribute '$attr_key' should match supplied value");
			}
		}

		if (isset($expected_flags) && !empty($expected_flags)) {
			foreach ($expected_flags as $flag_key => $flag_val) {
				$this->assertSame($flag_val, $cookie->flags[$flag_key], "Flag '$flag_key' should match supplied value");
			}
		}
	}
}
