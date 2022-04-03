<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers WpOrg\Requests\Cookie::uri_matches
 */
final class UriMatchesTest extends TestCase {

	/**
	 * @dataProvider dataUrlMatch
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
	 * @dataProvider dataUrlMatch
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

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataUrlMatch() {
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
}
