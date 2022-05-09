<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers \WpOrg\Requests\Cookie::domain_matches
 */
final class DomainMatchesTest extends TestCase {

	/**
	 * Manually set cookies without a domain/path set should always be valid.
	 *
	 * Cookies parsed from headers internally in Requests will always have a
	 * domain/path set, but those created manually will not. Manual cookies
	 * should be regarded as "global" cookies (that is, set for `.`).
	 */
	public function testManuallySetCookie() {
		$cookie = new Cookie('requests-testcookie', 'testvalue');
		$this->assertTrue($cookie->domain_matches('example.com'));
		$this->assertTrue($cookie->domain_matches('example.net'));
	}

	/**
	 * @dataProvider dataDomainMatch
	 */
	public function testDomainExactMatch($original, $check, $matches, $domain_matches) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = $original;
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertSame($matches, $cookie->domain_matches($check));
	}

	/**
	 * @dataProvider dataDomainMatch
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

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataDomainMatch() {
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
}
