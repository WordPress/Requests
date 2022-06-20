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
	 * Verify uri_matches() correctly identifies exact URI matches.
	 *
	 * @dataProvider dataUrlMatch
	 *
	 * @param string $domain         Base domain.
	 * @param string $path           Path to be appended to the domain.
	 * @param string $check          The URI to verify for a match.
	 * @param bool   $matches        The expected function return value for an exact match.
	 * @param bool   $domain_matches The expected function return value for a domain only match.
	 *
	 * @return void
	 */
	public function testUrlExactMatch($domain, $path, $check, $matches, $domain_matches) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = $domain;
		$attributes['path']   = $path;

		$check  = new Iri($check);
		$cookie = new Cookie('requests-testcookie', 'testvalue', $attributes);

		$this->assertSame($matches, $cookie->uri_matches($check));
	}

	/**
	 * Verify uri_matches() correctly identifies URI matches disregarding subdomains.
	 *
	 * @dataProvider dataUrlMatch
	 *
	 * @param string $domain         Base domain.
	 * @param string $path           Path to be appended to the domain.
	 * @param string $check          The URI to verify for a match.
	 * @param bool   $matches        The expected function return value for an exact match.
	 * @param bool   $domain_matches The expected function return value for a domain only match.
	 *
	 * @return void
	 */
	public function testUrlMatch($domain, $path, $check, $matches, $domain_matches) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = $domain;
		$attributes['path']   = $path;
		$flags                = [
			'host-only' => false,
		];

		$check  = new Iri($check);
		$cookie = new Cookie('requests-testcookie', 'testvalue', $attributes, $flags);

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

	/**
	 * Verify identifying URI matches correctly when the secure attribute is set.
	 *
	 * @dataProvider dataUrlMatchSecure
	 *
	 * @param bool   $secure   Value for the secure attribute.
	 * @param string $scheme   Which scheme to use in the check.
	 * @param bool   $expected The expected function return value.
	 *
	 * @return void
	 */
	public function testUrlMatchSecure($secure, $scheme, $expected) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = 'example.com';
		$attributes['path']   = '/';
		$attributes['secure'] = $secure;
		$flags                = [
			'host-only' => false,
		];
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes, $flags);

		$this->assertSame($expected, $cookie->uri_matches(new Iri($scheme . '://example.com/')), 'Expectation for exact match failed');

		// Double-check host-only.
		$this->assertSame($expected, $cookie->uri_matches(new Iri($scheme . '://www.example.com/')), 'Expectation for domain only match failed');
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataUrlMatchSecure() {
		return [
			'Secure matching: off, scheme: http' => [
				'secure'    => false,
				'scheme'    => 'http',
				'expected'  => true,
			],
			'Secure matching: off, scheme: https' => [
				'secure'    => false,
				'scheme'    => 'https',
				'expected'  => true,
			],
			'Secure matching: on, scheme: http' => [
				'secure'    => true,
				'scheme'    => 'http',
				'expected'  => false,
			],
			'Secure matching: on, scheme: https' => [
				'secure'    => true,
				'scheme'    => 'https',
				'expected'  => true,
			],
		];
	}

	/**
	 * Manually set cookies without a domain/path set should always be valid.
	 *
	 * Cookies parsed from headers internally in Requests will always have a
	 * domain/path set, but those created manually will not. Manual cookies
	 * should be regarded as "global" cookies (that is, set for `.`).
	 *
	 * @dataProvider dataManuallySetCookie
	 *
	 * @param string $url The URL to verify for a match.
	 *
	 * @return void
	 */
	public function testManuallySetCookie($url) {
		$cookie = new Cookie('requests-testcookie', 'testvalue');
		$iri    = new Iri($url);

		$this->assertTrue($cookie->uri_matches($iri));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataManuallySetCookie() {
		$urls = [
			'http://example.com',
			'http://example.com/',
			'http://example.com/test',
			'http://example.com/test/',
			'http://example.net/',
			'http://example.net/test',
			'http://example.net/test/',
		];

		return $this->textArrayToDataprovider($urls);
	}
}
