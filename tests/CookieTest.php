<?php

namespace WpOrg\Requests\Tests;

use DateTime;
use EmptyIterator;
use stdClass;
use WpOrg\Requests\Cookie;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\Fixtures\ArrayAccessibleObject;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

final class CookieTest extends TestCase {
	public function testBasicCookie() {
		$cookie = new Cookie('requests-testcookie', 'testvalue');

		$this->assertSame('requests-testcookie', $cookie->name);
		$this->assertSame('testvalue', $cookie->value);
		$this->assertSame('testvalue', (string) $cookie);

		$this->assertSame('requests-testcookie=testvalue', $cookie->format_for_header());
		$this->assertSame('requests-testcookie=testvalue', $cookie->format_for_set_cookie());
	}

	public function testCookieWithAttributes() {
		$attributes = [
			'httponly',
			'path' => '/',
		];
		$cookie     = new Cookie('requests-testcookie', 'testvalue', $attributes);

		$this->assertSame('requests-testcookie=testvalue', $cookie->format_for_header());
		$this->assertSame('requests-testcookie=testvalue; httponly; path=/', $cookie->format_for_set_cookie());
	}

	public function testEmptyCookieName() {
		$cookie = Cookie::parse('test');
		$this->assertSame('', $cookie->name);
		$this->assertSame('test', $cookie->value);
	}

	public function testEmptyAttributes() {
		$cookie = Cookie::parse('foo=bar; HttpOnly');
		$this->assertTrue($cookie->attributes['httponly']);
	}

	public function testReceivingCookies() {
		$options = [
			'follow_redirects' => false,
		];
		$url     = $this->httpbin('/cookies/set?requests-testcookie=testvalue');

		$response = Requests::get($url, [], $options);

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertNotEmpty($cookie);
		$this->assertSame('testvalue', $cookie->value);
	}

	public function testPersistenceOnRedirect() {
		$options = [
			'follow_redirects' => true,
		];
		$url     = $this->httpbin('/cookies/set?requests-testcookie=testvalue');

		$response = Requests::get($url, [], $options);

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertNotEmpty($cookie);
		$this->assertSame('testvalue', $cookie->value);
	}

	private function setCookieRequest($cookies) {
		$options  = [
			'cookies' => $cookies,
		];
		$response = Requests::get($this->httpbin('/cookies/set'), [], $options);

		$data = json_decode($response->body, true);
		$this->assertIsArray($data);
		$this->assertArrayHasKey('cookies', $data);
		return $data['cookies'];
	}

	public function testSendingCookie() {
		$cookies = [
			'requests-testcookie1' => 'testvalue1',
		];

		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertSame('testvalue1', $data['requests-testcookie1']);
	}

	/**
	 * @depends testSendingCookie
	 */
	public function testCookieExpiration() {
		$options = [
			'follow_redirects' => true,
		];
		$url     = $this->httpbin('/cookies/set/testcookie/testvalue');
		$url    .= '?expiry=1';

		$response = Requests::get($url, [], $options);
		$response->throw_for_status();

		$data = json_decode($response->body, true);
		$this->assertEmpty($data['cookies']);
	}

	public function testSendingMultipleCookies() {
		$cookies = [
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		];
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertSame('testvalue1', $data['requests-testcookie1']);

		$this->assertArrayHasKey('requests-testcookie2', $data);
		$this->assertSame('testvalue2', $data['requests-testcookie2']);
	}

	public function domainMatchProvider() {
		return [
			'Invalid check domain (type): null'         => ['example.com', null, false, false],
			'Invalid check domain (type): boolean true' => ['example.com', true, false, false],
			['example.com', 'example.com', true, true],
			['example.com', 'www.example.com', false, true],
			['example.com', 'example.net', false, false],

			// Leading period
			['.example.com', 'example.com', true, true],
			['.example.com', 'www.example.com', false, true],
			['.example.com', 'example.net', false, false],

			// Prefix, but not subdomain
			['example.com', 'notexample.com', false, false],
			['example.com', 'notexample.net', false, false],

			// Reject IP address prefixes
			['127.0.0.1', '127.0.0.1', true, true],
			['127.0.0.1', 'abc.127.0.0.1', false, false],
			['127.0.0.1', 'example.com', false, false],

			// Check that we're checking the actual length
			['127.com', 'test.127.com', false, true],
		];
	}

	/**
	 * @dataProvider domainMatchProvider
	 */
	public function testDomainExactMatch($original, $check, $matches, $domain_matches) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = $original;
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertSame($matches, $cookie->domain_matches($check));
	}

	/**
	 * @dataProvider domainMatchProvider
	 */
	public function testDomainMatch($original, $check, $matches, $domain_matches) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = $original;
		$flags                = [
			'host-only' => false,
		];
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes, $flags);
		$this->assertSame($domain_matches, $cookie->domain_matches($check));
	}

	public function pathMatchProvider() {
		return [
			'Invalid check path (type): null'    => ['/', null, true],
			'Invalid check path (type): true'    => ['/', true, false],
			'Invalid check path (type): integer' => ['/', 123, false],
			'Invalid check path (type): array'   => ['/', [1, 2], false],
			['/', '', true],
			['/', '/', true],

			['/', '/test', true],
			['/', '/test/', true],

			['/test', '/', false],
			['/test', '/test', true],
			['/test', '/testing', false],
			['/test', '/test/', true],
			['/test', '/test/ing', true],
			['/test', '/test/ing/', true],

			['/test/', '/test/', true],
			['/test/', '/', false],
		];
	}

	/**
	 * @dataProvider pathMatchProvider
	 */
	public function testPathMatch($original, $check, $matches) {
		$attributes         = new CaseInsensitiveDictionary();
		$attributes['path'] = $original;
		$cookie             = new Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertSame($matches, $cookie->path_matches($check));
	}

	public function urlMatchProvider() {
		return [
			// Domain handling
			['example.com', '/', 'http://example.com/', true, true],
			['example.com', '/', 'http://www.example.com/', false, true],
			['example.com', '/', 'http://example.net/', false, false],
			['example.com', '/', 'http://www.example.net/', false, false],

			// /test
			['example.com', '/test', 'http://example.com/', false, false],
			['example.com', '/test', 'http://www.example.com/', false, false],

			['example.com', '/test', 'http://example.com/test', true, true],
			['example.com', '/test', 'http://www.example.com/test', false, true],

			['example.com', '/test', 'http://example.com/testing', false, false],
			['example.com', '/test', 'http://www.example.com/testing', false, false],

			['example.com', '/test', 'http://example.com/test/', true, true],
			['example.com', '/test', 'http://www.example.com/test/', false, true],

			// /test/
			['example.com', '/test/', 'http://example.com/', false, false],
			['example.com', '/test/', 'http://www.example.com/', false, false],
		];
	}

	/**
	 * @depends testDomainExactMatch
	 * @depends testPathMatch
	 * @dataProvider urlMatchProvider
	 */
	public function testUrlExactMatch($domain, $path, $check, $matches, $domain_matches) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = $domain;
		$attributes['path']   = $path;
		$check                = new Iri($check);
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertSame($matches, $cookie->uri_matches($check));
	}

	/**
	 * @depends testDomainMatch
	 * @depends testPathMatch
	 * @dataProvider urlMatchProvider
	 */
	public function testUrlMatch($domain, $path, $check, $matches, $domain_matches) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = $domain;
		$attributes['path']   = $path;
		$flags                = [
			'host-only' => false,
		];
		$check                = new Iri($check);
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes, $flags);
		$this->assertSame($domain_matches, $cookie->uri_matches($check));
	}

	public function testUrlMatchSecure() {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = 'example.com';
		$attributes['path']   = '/';
		$attributes['secure'] = true;
		$flags                = [
			'host-only' => false,
		];
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes, $flags);

		$this->assertTrue($cookie->uri_matches(new Iri('https://example.com/')));
		$this->assertFalse($cookie->uri_matches(new Iri('http://example.com/')));

		// Double-check host-only
		$this->assertTrue($cookie->uri_matches(new Iri('https://www.example.com/')));
		$this->assertFalse($cookie->uri_matches(new Iri('http://www.example.com/')));
	}

	/**
	 * Manually set cookies without a domain/path set should always be valid
	 *
	 * Cookies parsed from headers internally in Requests will always have a
	 * domain/path set, but those created manually will not. Manual cookies
	 * should be regarded as "global" cookies (that is, set for `.`)
	 */
	public function testUrlMatchManuallySet() {
		$cookie = new Cookie('requests-testcookie', 'testvalue');
		$this->assertTrue($cookie->domain_matches('example.com'));
		$this->assertTrue($cookie->domain_matches('example.net'));
		$this->assertTrue($cookie->path_matches('/'));
		$this->assertTrue($cookie->path_matches('/test'));
		$this->assertTrue($cookie->path_matches('/test/'));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.com/')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.com/test')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.com/test/')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.net/')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.net/test')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.net/test/')));
	}

	public static function parseResultProvider() {
		return [
			// Basic parsing
			[
				'foo=bar',
				['name' => 'foo', 'value' => 'bar'],
			],
			[
				'bar',
				['name' => '', 'value' => 'bar'],
			],

			// Expiration
			// RFC 822, updated by RFC 1123
			[
				'foo=bar; Expires=Thu, 5-Dec-2013 04:50:12 GMT',
				['expired' => true],
				['expires' => gmmktime(4, 50, 12, 12, 5, 2013)],
			],
			[
				'foo=bar; Expires=Fri, 5-Dec-2014 04:50:12 GMT',
				['expired' => false],
				['expires' => gmmktime(4, 50, 12, 12, 5, 2014)],
			],
			// RFC 850, obsoleted by RFC 1036
			[
				'foo=bar; Expires=Thursday, 5-Dec-2013 04:50:12 GMT',
				['expired' => true],
				['expires' => gmmktime(4, 50, 12, 12, 5, 2013)],
			],
			[
				'foo=bar; Expires=Friday, 5-Dec-2014 04:50:12 GMT',
				['expired' => false],
				['expires' => gmmktime(4, 50, 12, 12, 5, 2014)],
			],
			// Test with asctime()
			[
				'foo=bar; Expires=Thu Dec  5 04:50:12 2013',
				['expired' => true],
				['expires' => gmmktime(4, 50, 12, 12, 5, 2013)],
			],
			[
				'foo=bar; Expires=Fri Dec  5 04:50:12 2014',
				['expired' => false],
				['expires' => gmmktime(4, 50, 12, 12, 5, 2014)],
			],
			[
				// Invalid
				'foo=bar; Expires=never',
				[],
				['expires' => null],
			],

			// Max-Age
			[
				'foo=bar; Max-Age=10',
				['expired' => false],
				['max-age' => gmmktime(0, 0, 10, 1, 1, 2014)],
			],
			[
				'foo=bar; Max-Age=3660',
				['expired' => false],
				['max-age' => gmmktime(1, 1, 0, 1, 1, 2014)],
			],
			[
				'foo=bar; Max-Age=0',
				['expired' => true],
				['max-age' => 0],
			],
			[
				'foo=bar; Max-Age=-1000',
				['expired' => true],
				['max-age' => 0],
			],
			[
				// Invalid (non-digit character)
				'foo=bar; Max-Age=1e6',
				['expired' => false],
				['max-age' => null],
			],
		];
	}

	private function check_parsed_cookie($cookie, $expected, $expected_attributes, $expected_flags = []) {
		if (isset($expected['name'])) {
			$this->assertSame($expected['name'], $cookie->name);
		}

		if (isset($expected['value'])) {
			$this->assertSame($expected['value'], $cookie->value);
		}

		if (isset($expected['expired'])) {
			$this->assertSame($expected['expired'], $cookie->is_expired());
		}

		if (isset($expected_attributes)) {
			foreach ($expected_attributes as $attr_key => $attr_val) {
				$this->assertSame($attr_val, $cookie->attributes[$attr_key], "$attr_key should match supplied");
			}
		}

		if (isset($expected_flags)) {
			foreach ($expected_flags as $flag_key => $flag_val) {
				$this->assertSame($flag_val, $cookie->flags[$flag_key], "$flag_key should match supplied");
			}
		}
	}

	/**
	 * @dataProvider parseResultProvider
	 */
	public function testParsingHeader($header, $expected, $expected_attributes = [], $expected_flags = []) {
		// Set the reference time to 2014-01-01 00:00:00
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2014);

		$cookie = Cookie::parse($header, '', $reference_time);
		$this->check_parsed_cookie($cookie, $expected, $expected_attributes);
	}

	/**
	 * Double-normalizes the cookie data to ensure we catch any issues there
	 *
	 * @dataProvider parseResultProvider
	 */
	public function testParsingHeaderDouble($header, $expected, $expected_attributes = [], $expected_flags = []) {
		// Set the reference time to 2014-01-01 00:00:00
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2014);

		$cookie = Cookie::parse($header, '', $reference_time);

		// Normalize the value again
		$cookie->normalize();

		$this->check_parsed_cookie($cookie, $expected, $expected_attributes, $expected_flags);
	}

	/**
	 * @dataProvider parseResultProvider
	 */
	public function testParsingHeaderObject($header, $expected, $expected_attributes = [], $expected_flags = []) {
		$headers               = new Headers();
		$headers['Set-Cookie'] = $header;

		// Set the reference time to 2014-01-01 00:00:00
		$reference_time = gmmktime(0, 0, 0, 1, 1, 2014);

		$parsed = Cookie::parse_from_headers($headers, null, $reference_time);
		$this->assertCount(1, $parsed);

		$cookie = reset($parsed);
		$this->check_parsed_cookie($cookie, $expected, $expected_attributes);
	}

	public function parseFromHeadersProvider() {
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
	 * @dataProvider parseFromHeadersProvider
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
	 * Tests receiving an exception when the constructor received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @covers \WpOrg\Requests\Cookie::__construct
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testConstructorInvalidName($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($name) must be of type string');

		new Cookie($input, 'value');
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$value`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @covers \WpOrg\Requests\Cookie::__construct
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testConstructorInvalidValue($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($value) must be of type string');

		new Cookie('name', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidStringInput() {
		return [
			'null'              => [null],
			'float'             => [1.1],
			'stringable object' => [new StringableObject('name')],
		];
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$name`.
	 *
	 * @dataProvider dataConstructorInvalidAttributes
	 *
	 * @covers \WpOrg\Requests\Cookie::__construct
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testConstructorInvalidAttributes($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #3 ($attributes) must be of type array|ArrayAccess&Traversable');

		new Cookie('name', 'value', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataConstructorInvalidAttributes() {
		return [
			'null'                                 => [null],
			'text string'                          => ['array'],
			'iterator object without array access' => [new EmptyIterator()],
			'array accessible object not iterable' => [new ArrayAccessibleObject([1, 2, 3])],
		];
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$flags`.
	 *
	 * @dataProvider dataConstructorInvalidFlags
	 *
	 * @covers \WpOrg\Requests\Cookie::__construct
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testConstructorInvalidFlags($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #4 ($flags) must be of type array');

		new Cookie('name', 'value', [], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataConstructorInvalidFlags() {
		return [
			'null'                    => [null],
			'integer'                 => [101],
			'array accessible object' => [new ArrayAccessibleObject([])],
		];
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$reference_time`.
	 *
	 * @dataProvider dataConstructorInvalidReferenceTime
	 *
	 * @covers \WpOrg\Requests\Cookie::__construct
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testConstructorInvalidReferenceTime($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #5 ($reference_time) must be of type integer|null');

		new Cookie('name', 'value', [], [], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataConstructorInvalidReferenceTime() {
		return [
			'float'           => [1.1],
			'string'          => ['now'],
			'DateTime object' => [new DateTime('now')],
		];
	}

	/**
	 * Tests receiving an exception when the parse() method received an invalid input type as `$cookie_header`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @covers \WpOrg\Requests\Cookie::parse
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
	 * @covers \WpOrg\Requests\Cookie::parse
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
	 * Tests receiving an exception when the parse() method received an invalid input type as `$reference_time`.
	 *
	 * @covers \WpOrg\Requests\Cookie::parse
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
	 * @covers \WpOrg\Requests\Cookie::parse_from_headers
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
	 * Verify parsing of cookies fails with an exception if the $origin parameter is passed anything but `null`
	 * or an instance of Iri.
	 *
	 * @dataProvider dataParseFromHeadersInvalidOrigin
	 *
	 * @covers ::parse_from_headers
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testParseFromHeadersInvalidOrigin($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($origin) must be of type WpOrg\Requests\Iri or null');

		$headers               = new Headers();
		$headers['Set-Cookie'] = 'name=value';

		Cookie::parse_from_headers($headers, $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataParseFromHeadersInvalidOrigin() {
		return [
			'falseg'   => [false],
			'string'   => ['something'],
			'stdClass' => [new stdClass()],
		];
	}
}
