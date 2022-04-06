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
			// Domain handling.
			'Domain handling: same domain name, same TLD' => [
				'domain'         => 'example.com',
				'path'           => '/',
				'check'          => 'http://example.com/',
				'matched'        => true,
				'domain_matches' => true,
			],
			'Domain handling: same domain name, same TLD, different subdomain' => [
				'domain'         => 'example.com',
				'path'           => '/',
				'check'          => 'http://www.example.com/',
				'matched'        => false,
				'domain_matches' => true,
			],
			'Domain handling: same domain name, different TLD' => [
				'domain'         => 'example.com',
				'path'           => '/',
				'check'          => 'http://example.net/',
				'matched'        => false,
				'domain_matches' => false,
			],
			'Domain handling: same domain name, different TLD, different subdomain' => [
				'domain'         => 'example.com',
				'path'           => '/',
				'check'          => 'http://www.example.net/',
				'matched'        => false,
				'domain_matches' => false,
			],

			// Path handling - /test (no trailing slash).
			'Path handling "/test": same domain name, same TLD, URI provided without path' => [
				'domain'         => 'example.com',
				'path'           => '/test',
				'check'          => 'http://example.com/',
				'matched'        => false,
				'domain_matches' => false,
			],
			'Path handling "/test": same domain name, same TLD, different subdomain, URI provided without path' => [
				'domain'         => 'example.com',
				'path'           => '/test',
				'check'          => 'http://www.example.com/',
				'matched'        => false,
				'domain_matches' => false,
			],

			'Path handling "/test": same domain name, same TLD, URI provided including path' => [
				'domain'         => 'example.com',
				'path'           => '/test',
				'check'          => 'http://example.com/test',
				'matched'        => true,
				'domain_matches' => true,
			],
			'Path handling "/test": same domain name, same TLD, different subdomain, URI provided including path' => [
				'domain'         => 'example.com',
				'path'           => '/test',
				'check'          => 'http://www.example.com/test',
				'matched'        => false,
				'domain_matches' => true,
			],

			'Path handling "/test": same domain name, same TLD, different path' => [
				'domain'         => 'example.com',
				'path'           => '/test',
				'check'          => 'http://example.com/testing',
				'matched'        => false,
				'domain_matches' => false,
			],
			'Path handling "/test": same domain name, same TLD, different subdomain, different path' => [
				'domain'         => 'example.com',
				'path'           => '/test',
				'check'          => 'http://www.example.com/testing',
				'matched'        => false,
				'domain_matches' => false,
			],

			'Path handling "/test": same domain name, same TLD, URI provided including path with trailing slash' => [
				'domain'         => 'example.com',
				'path'           => '/test',
				'check'          => 'http://example.com/test/',
				'matched'        => true,
				'domain_matches' => true,
			],
			'Path handling "/test": same domain name, same TLD, different subdomain, URI provided including path with trailing slash' => [
				'domain'         => 'example.com',
				'path'           => '/test',
				'check'          => 'http://www.example.com/test/',
				'matched'        => false,
				'domain_matches' => true,
			],

			// Path handling - /test/ (with trailing slash).
			'Path handling "/test/" (incl trailing slash): same domain name, same TLD, URI provided without path' => [
				'domain'         => 'example.com',
				'path'           => '/test/',
				'check'          => 'http://example.com/',
				'matched'        => false,
				'domain_matches' => false,
			],
			'Path handling "/test/" (incl trailing slash): same domain name, same TLD, different subdomain, URI provided without path' => [
				'domain'         => 'example.com',
				'path'           => '/test/',
				'check'          => 'http://www.example.com/',
				'matched'        => false,
				'domain_matches' => false,
			],
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

	/**
	 * Manually set cookies without a domain/path set should always be valid.
	 *
	 * Cookies parsed from headers internally in Requests will always have a
	 * domain/path set, but those created manually will not. Manual cookies
	 * should be regarded as "global" cookies (that is, set for `.`).
	 */
	public function testManuallySetCookie() {
		$cookie = new Cookie('requests-testcookie', 'testvalue');
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.com/')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.com/test')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.com/test/')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.net/')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.net/test')));
		$this->assertTrue($cookie->uri_matches(new Iri('http://example.net/test/')));
	}
}
