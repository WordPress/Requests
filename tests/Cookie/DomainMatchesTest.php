<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers \WpOrg\Requests\Cookie::domain_matches
 */
final class DomainMatchesTest extends TestCase {

	/**
	 * Verify that invalid input will always result in a non-match.
	 *
	 * @dataProvider dataInvalidInput
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidInput($input) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = 'example.com';
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes);

		$this->assertFalse($cookie->domain_matches($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInput() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
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
	 * @param string $domain Domain to verify for a match.
	 *
	 * @return void
	 */
	public function testManuallySetCookie($domain) {
		$cookie = new Cookie('requests-testcookie', 'testvalue');

		$this->assertTrue($cookie->domain_matches($domain));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataManuallySetCookie() {
		$domains = [
			'example.com',
			'example.net',
		];

		return $this->textArrayToDataprovider($domains);
	}

	/**
	 * Verify domain_matches() correctly identifies exact domain matches.
	 *
	 * @dataProvider dataDomainMatch
	 *
	 * @param string $original       Original, known domain.
	 * @param string $check          Domain to verify for a match.
	 * @param bool   $matches        The expected function return value for an exact match.
	 * @param bool   $domain_matches The expected function return value for a domain only match.
	 *
	 * @return void
	 */
	public function testDomainExactMatch($original, $check, $matches, $domain_matches) {
		$attributes           = new CaseInsensitiveDictionary();
		$attributes['domain'] = $original;
		$cookie               = new Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertSame($matches, $cookie->domain_matches($check));
	}

	/**
	 * Verify domain_matches() correctly identifies domain matches disregarding subdomains.
	 *
	 * @dataProvider dataDomainMatch
	 *
	 * @param string $original       Original, known domain.
	 * @param string $check          Domain to verify for a match.
	 * @param bool   $matches        The expected function return value for an exact match.
	 * @param bool   $domain_matches The expected function return value for a domain only match.
	 *
	 * @return void
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
			'Empty string' => [
				'original'       => 'example.com',
				'check'          => '',
				'matches'        => false,
				'domain_matches' => false,
			],

			'Domain: exact match' => [
				'original'       => 'example.com',
				'check'          => 'example.com',
				'matches'        => true,
				'domain_matches' => true,
			],
			'Domain: same domain, same TLD, different subdomain' => [
				'original'       => 'example.com',
				'check'          => 'www.example.com',
				'matches'        => false,
				'domain_matches' => true,
			],
			'Domain: same domain, different TLD' => [
				'original'       => 'example.com',
				'check'          => 'example.net',
				'matches'        => false,
				'domain_matches' => false,
			],

			// Leading period.
			'Original domain with leading period, otherwise exact match' => [
				'original'       => '.example.com',
				'check'          => 'example.com',
				'matches'        => true,
				'domain_matches' => true,
			],
			'Original domain with leading period, same domain, same TLD, different subdomain' => [
				'original'       => '.example.com',
				'check'          => 'www.example.com',
				'matches'        => false,
				'domain_matches' => true,
			],
			'Original domain with leading period, same domain, different TLD' => [
				'original'       => '.example.com',
				'check'          => 'example.net',
				'matches'        => false,
				'domain_matches' => false,
			],

			// Prefix, but not subdomain.
			'Overlap: different domain with original domain substring of check' => [
				'original'       => 'example.com',
				'check'          => 'notexample.com',
				'matches'        => false,
				'domain_matches' => false,
			],
			'Overlap: different domain with original domain substring of check, different TLD' => [
				'original'       => 'example.com',
				'check'          => 'notexample.net',
				'matches'        => false,
				'domain_matches' => false,
			],

			// Reject IP address prefixes.
			'IP addresses: exact match' => [
				'original'       => '127.0.0.1',
				'check'          => '127.0.0.1',
				'matches'        => true,
				'domain_matches' => true,
			],
			'IP address: original is IP, check appears to have subdomain prefix' => [
				'original'       => '127.0.0.1',
				'check'          => 'abc.127.0.0.1',
				'matches'        => false,
				'domain_matches' => false,
			],
			'IP addresses: non-matching' => [
				'original'       => '127.0.0.1',
				'check'          => 'example.com',
				'matches'        => false,
				'domain_matches' => false,
			],

			// Check that we're checking the actual length.
			'Actual length check' => [
				'original'       => '127.com',
				'check'          => 'test.127.com',
				'matches'        => false,
				'domain_matches' => true,
			],
			'Length check: check domain shorter than original' => [
				'original'       => '127.com',
				'check'          => '27.com',
				'matches'        => false,
				'domain_matches' => false,
			],
		];
	}
}
