<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Ssl;
use WpOrg\Requests\Tests\TestCase;

final class SslTest extends TestCase {

	/**
	 * @dataProvider domainMatchProvider
	 */
	public function testMatch($base, $dnsname) {
		$this->assertTrue(Ssl::match_domain($base, $dnsname));
	}

	/**
	 * @dataProvider domainMatchProvider
	 */
	public function testMatchViaCertificate($base, $dnsname) {
		$certificate = $this->fakeCertificate($dnsname);
		$this->assertTrue(Ssl::verify_certificate($base, $certificate));
	}

	public static function domainMatchProvider() {
		return array(
			array('example.com', 'example.com'),
			array('test.example.com', 'test.example.com'),
			array('test.example.com', '*.example.com'),
		);
	}

	/**
	 * @dataProvider domainNoMatchProvider
	 */
	public function testNoMatch($base, $dnsname) {
		$this->assertFalse(Ssl::match_domain($base, $dnsname));
	}

	/**
	 * @dataProvider domainNoMatchProvider
	 */
	public function testNoMatchViaCertificate($base, $dnsname) {
		$certificate = $this->fakeCertificate($dnsname);
		$this->assertFalse(Ssl::verify_certificate($base, $certificate));
	}

	public static function domainNoMatchProvider() {
		return array(
			// Check that we need at least 3 components
			array('com', '*'),
			array('example.com', '*.com'),

			// Check that double wildcards don't work
			array('abc.def.example.com', '*.*.example.com'),

			// Check that we only match with the correct number of components
			array('abc.def.example.com', 'def.example.com'),
			array('abc.def.example.com', '*.example.com'),

			// Check that the wildcard only works as the full first component
			array('abc.def.example.com', 'a*.def.example.com'),

			// Check that wildcards are not allowed for IPs
			array('192.168.0.1', '*.168.0.1'),
			array('192.168.0.1', '192.168.0.*'),
		);
	}

	private function fakeCertificate($dnsname, $with_san = true) {
		$certificate = array(
			'subject' => array(
				'CN' => $dnsname,
			),
		);

		if ($with_san !== false) {
			// If SAN is set to true, default it to the dNSName
			if ($with_san === true) {
				$with_san = $dnsname;
			}
			$certificate['extensions'] = array(
				'subjectAltName' => 'DNS: ' . $with_san,
			);
		}

		return $certificate;
	}

	public function testCNFallback() {
		$certificate = $this->fakeCertificate('example.com', false);
		$this->assertTrue(Ssl::verify_certificate('example.com', $certificate));
	}

	public function testInvalidCNFallback() {
		$certificate = $this->fakeCertificate('example.com', false);
		$this->assertFalse(Ssl::verify_certificate('example.net', $certificate));
	}

	/**
	 * Test a certificate with both CN and SAN fields
	 *
	 * As per RFC2818, if the SAN field exists, we should parse that and ignore
	 * the value of the CN field.
	 *
	 * @link https://tools.ietf.org/html/rfc2818#section-3.1
	 */
	public function testIgnoreCNWithSAN() {
		$certificate = $this->fakeCertificate('example.net', 'example.com');

		$this->assertTrue(Ssl::verify_certificate('example.com', $certificate), 'Checking SAN validation');
		$this->assertFalse(Ssl::verify_certificate('example.net', $certificate), 'Checking CN non-validation');
	}
}
