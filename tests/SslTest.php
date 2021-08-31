<?php

namespace WpOrg\Requests\Tests;

use stdClass;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Ssl;
use WpOrg\Requests\Tests\TestCase;

/**
 * @coversDefaultClass \WpOrg\Requests\Ssl
 */
final class SslTest extends TestCase {

	/**
	 * Test handling of matching host and DNS names.
	 *
	 * @dataProvider dataMatch
	 *
	 * @covers ::match_domain
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
	 * @covers ::verify_certificate
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
			'SAN available, multiple alternatives provided; matching is second' => array(
				'host'      => 'example.net',
				'reference' => 'example.net',
				'with_san'  => 'DNS: example.com, DNS: example.net, DNS: example.info',
			),
			'SAN available, multiple alternatives provided; matching is last' => array(
				'host'      => 'example.net',
				'reference' => 'example.net',
				'with_san'  => 'DNS: example.com, DNS: example.org, DNS: example.net',
			),
			'SAN available, DNS prefix missing in first, not (matching) second' => array(
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => 'example.com, DNS: example.net',
			),
			'SAN available, DNS prefix missing in all, fallback to CN' => array(
				'host'      => 'example.net',
				'reference' => 'example.net',
				'with_san'  => 'example.com, example.net',
			),
		);
	}

	/**
	 * Test handling of non-matching host and DNS names.
	 *
	 * @dataProvider dataNoMatch
	 *
	 * @covers ::match_domain
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
	 * @covers ::verify_certificate
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
			'SAN empty' => array(
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => '',
			),
			'SAN available, DNS prefix missing' => array(
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => 'example.com',
			),
			'SAN available, multiple alternatives provided; none of the SANs match, DNS prefix missing in first, not second' => array(
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => 'example.com, DNS: example.org',
			),
			'SAN available, multiple alternatives provided; none of the SANs match, even though CN does' => array(
				'host'      => 'example.net',
				'reference' => 'example.net',
				'with_san'  => 'DNS: example.com, DNS: example.org, DNS: example.info',
			),
		);
	}

	/**
	 * Test helper to mock a certificate.
	 *
	 * @return array
	 */
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
	 *
	 * @covers ::verify_certificate
	 */
	public function testIgnoreCNWithSAN() {
		$certificate = $this->fakeCertificate('example.net', 'DNS: example.com');

		$this->assertTrue(Ssl::verify_certificate('example.com', $certificate), 'Checking SAN validation');
		$this->assertFalse(Ssl::verify_certificate('example.net', $certificate), 'Checking CN non-validation');
	}

	/**
	 * Test handling of non-compliant certificates.
	 *
	 * @dataProvider dataVerifyCertificateWithInvalidCertificates
	 *
	 * @param string $host        Host name to verify.
	 * @param array  $certificate A (faked) certificate to verify against.
	 * @param bool   $expected    Expected function output.
	 *
	 * @return void
	 */
	public function testVerifyCertificateWithInvalidCertificates($host, $certificate, $expected) {
		$this->assertSame($expected, Ssl::verify_certificate($host, $certificate));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataVerifyCertificateWithInvalidCertificates() {
		return array(
			'empty array' => array(
				'host'        => 'example.com',
				'certificate' => array(),
				'expected'    => false,
			),
			'subject, but missing CN entry; no SAN' => array(
				'host'        => 'example.com',
				'certificate' => array(
					'subject' => array(),
				),
				'expected'    => false,
			),
			'subject with empty CN entry; no SAN' => array(
				'host'        => 'example.com',
				'certificate' => array(
					'subject' => array(
						'CN' => '',
					),
				),
				'expected'    => false,
			),
			'subject, but missing CN entry; SAN exists, missing DNS' => array(
				'host'        => 'example.com',
				'certificate' => array(
					'subject'    => array(),
					'extensions' => array(
						'subjectAltName' => 'example.net',
					),
				),
				'expected'    => false,
			),
			'subject with empty CN entry; SAN exists, missing DNS' => array(
				'host'        => 'example.com',
				'certificate' => array(
					'subject' => array(
						'CN' => '',
					),
					'extensions' => array(
						'subjectAltName' => 'example.net',
					),
				),
				'expected'    => false,
			),
			'subject, but missing CN entry; SAN exists, but non-matching' => array(
				'host'        => 'example.com',
				'certificate' => array(
					'subject'    => array(),
					'extensions' => array(
						'subjectAltName' => 'DNS: example.net',
					),
				),
				'expected'    => false,
			),
			'subject with empty CN entry; SAN exists, but non-matching' => array(
				'host'        => 'example.com',
				'certificate' => array(
					'subject' => array(
						'CN' => '',
					),
					'extensions' => array(
						'subjectAltName' => 'DNS:   example.net',
					),
				),
				'expected'    => false,
			),
			'extensions, but missing SAN entry' => array(
				'host'        => 'example.com',
				'certificate' => array(
					'subject'    => array(
						'CN' => 'example.net',
					),
					'extensions' => array(),
				),
				'expected'    => false,
			),
			'extensions with empty SAN entry' => array(
				'host'        => 'example.com',
				'certificate' => array(
					'subject' => array(
						'CN' => 'example.net',
					),
					'extensions' => array(
						'subjectAltName' => '',
					),
				),
				'expected'    => false,
			),
		);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as $host.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @covers ::verify_certificate
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testVerifyCertificateInvalidInputHost($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($host) must be of type string|Stringable');

		Ssl::verify_certificate($input, array());
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as $cert.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @covers ::verify_certificate
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testVerifyCertificateInvalidInputCert($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($cert) must be of type array|ArrayAccess');

		Ssl::verify_certificate('host', $input);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @covers ::verify_reference_name
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testVerifyReferenceNameInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($reference) must be of type string|Stringable');

		Ssl::verify_reference_name($input);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @covers ::match_domain
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($host) must be of type string|Stringable');

		Ssl::match_domain($input, 'reference');
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInputType() {
		return array(
			'null'         => array(null),
			'plain object' => array(new stdClass()),
		);
	}
}
