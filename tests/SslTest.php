<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Ssl;
use WpOrg\Requests\Tests\TestCase;

final class SslTest extends TestCase {

	/**
	 * Test handling of matching host and DNS names.
	 *
	 * @dataProvider dataMatch
	 *
	 * @param string $host      Host name to verify.
	 * @param string $reference DNS name to match against.
	 *
	 * @return void
	 */
	public function testMatch($host, $reference) {
		$this->assertTrue(Ssl::match_domain($host, $reference));
	}

	/**
	 * Test handling of matching host and DNS names based on certificate.
	 *
	 * @dataProvider dataMatch
	 * @dataProvider dataMatchViaCertificate
	 *
	 * @param string      $host      Host name to verify.
	 * @param string      $reference DNS name to match against.
	 * @param bool|string $with_san  Optional. How to generate the fake certificate.
	 *                               - false:  plain, CN only;
	 *                               - true:   CN + subjectAltName, alt set to same as CN;
	 *                               - string: CN + subjectAltName, alt set to string value.
	 *
	 * @return void
	 */
	public function testMatchViaCertificate($host, $reference, $with_san = true) {
		$certificate = $this->fakeCertificate($reference, $with_san);
		$this->assertTrue(Ssl::verify_certificate($host, $certificate));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataMatch() {
		return array(
			'top-level domain' => array(
				'host'      => 'example.com',
				'reference' => 'example.com',
			),
			'subdomain' => array(
				'host'      => 'test.example.com',
				'reference' => 'test.example.com',
			),
			'subdomain with wildcard reference' => array(
				'host'      => 'test.example.com',
				'reference' => '*.example.com',
			),
		);
	}

	/**
	 * Data provider for additional test cases specific to the Ssl::verify_certificate() method.
	 *
	 * @return array
	 */
	public function dataMatchViaCertificate() {
		return array(
			'top-level domain; missing SAN, fallback to CN' => array(
				'host'      => 'example.com',
				'reference' => 'example.com',
				'with_san'  => false,
			),
		);
	}

	/**
	 * Test handling of non-matching host and DNS names.
	 *
	 * @dataProvider dataNoMatch
	 *
	 * @param string $host      Host name to verify.
	 * @param string $reference DNS name to match against.
	 *
	 * @return void
	 */
	public function testNoMatch($host, $reference) {
		$this->assertFalse(Ssl::match_domain($host, $reference));
	}

	/**
	 * Test handling of non-matching host and DNS names based on certificate.
	 *
	 * @dataProvider dataNoMatch
	 * @dataProvider dataNoMatchViaCertificate
	 *
	 * @param string      $host      Host name to verify.
	 * @param string      $reference DNS name to match against.
	 * @param bool|string $with_san  Optional. How to generate the fake certificate.
	 *                               - false:  plain, CN only;
	 *                               - true:   CN + subjectAltName, alt set to same as CN;
	 *                               - string: CN + subjectAltName, alt set to string value.
	 *
	 * @return void
	 */
	public function testNoMatchViaCertificate($host, $reference, $with_san = true) {
		$certificate = $this->fakeCertificate($reference, $with_san);
		$this->assertFalse(Ssl::verify_certificate($host, $certificate));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataNoMatch() {
		return array(
			// Check that we need at least 3 components
			'not a domain; wildcard reference' => array(
				'host'      => 'com',
				'reference' => '*',
			),
			'domain name; wildcard on TLD as reference' => array(
				'host'      => 'example.com',
				'reference' => '*.com',
			),

			// Check that double wildcards don't work
			'domain name; double wildcard in reference' => array(
				'host'      => 'abc.def.example.com',
				'reference' => '*.*.example.com',
			),

			// Check that we only match with the correct number of components
			'four-level domain; three-level reference' => array(
				'host'      => 'abc.def.example.com',
				'reference' => 'def.example.com',
			),
			'four-level domain; three-level wildcard reference' => array(
				'host'      => 'abc.def.example.com',
				'reference' => '*.example.com',
			),

			// Check that the wildcard only works as the full first component
			'four-level domain; four-level reference, but wildcard not stand-alone' => array(
				'host'      => 'abc.def.example.com',
				'reference' => 'a*.def.example.com',
			),

			// Check that wildcards are not allowed for IPs
			'IP address; wildcard in refence (start)' => array(
				'host'      => '192.168.0.1',
				'reference' => '*.168.0.1',
			),
			'IP address; wildcard in refence (end)' => array(
				'host'      => '192.168.0.1',
				'reference' => '192.168.0.*',
			),

			// IP vs named address.
			'IP address vs named address' => array(
				'host'      => '192.168.0.1',
				'reference' => '*.example.com',
			),
			'Named address vs IP address' => array(
				'host'      => 'example.com',
				'reference' => '192.168.0.1',
			),
		);
	}

	/**
	 * Data provider for additional test cases specific to the Ssl::verify_certificate() method.
	 *
	 * @return array
	 */
	public function dataNoMatchViaCertificate() {
		return array(
			'top-level domain; missing SAN, fallback to invalid CN' => array(
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => false,
			),
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
				$with_san = 'DNS: ' . $dnsname;
			}
			$certificate['extensions'] = array(
				'subjectAltName' => $with_san,
			);
		}

		return $certificate;
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
		$certificate = $this->fakeCertificate('example.net', 'DNS: example.com');

		$this->assertTrue(Ssl::verify_certificate('example.com', $certificate), 'Checking SAN validation');
		$this->assertFalse(Ssl::verify_certificate('example.net', $certificate), 'Checking CN non-validation');
	}
}
