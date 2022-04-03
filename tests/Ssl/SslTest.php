<?php

namespace WpOrg\Requests\Tests\Ssl;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Ssl;
use WpOrg\Requests\Tests\Ssl\SslTestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @coversDefaultClass \WpOrg\Requests\Ssl
 */
final class SslTest extends SslTestCase {

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
	 * Data provider for additional test cases specific to the Ssl::verify_certificate() method.
	 *
	 * @return array
	 */
	public function dataMatchViaCertificate() {
		return [
			'top-level domain; missing SAN, fallback to CN' => [
				'host'      => 'example.com',
				'reference' => 'example.com',
				'with_san'  => false,
			],
			'SAN available, multiple alternatives provided; matching is second' => [
				'host'      => 'example.net',
				'reference' => 'example.net',
				'with_san'  => 'DNS: example.com, DNS: example.net, DNS: example.info',
			],
			'SAN available, multiple alternatives provided; matching is last' => [
				'host'      => 'example.net',
				'reference' => 'example.net',
				'with_san'  => 'DNS: example.com, DNS: example.org, DNS: example.net',
			],
			'SAN available, DNS prefix missing in first, not (matching) second' => [
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => 'example.com, DNS: example.net',
			],
			'SAN available, DNS prefix missing in all, fallback to CN' => [
				'host'      => 'example.net',
				'reference' => 'example.net',
				'with_san'  => 'example.com, example.net',
			],
		];
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
	 * Data provider for additional test cases specific to the Ssl::verify_certificate() method.
	 *
	 * @return array
	 */
	public function dataNoMatchViaCertificate() {
		return [
			'top-level domain; missing SAN, fallback to invalid CN' => [
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => false,
			],
			'SAN empty' => [
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => '',
			],
			'SAN available, DNS prefix missing' => [
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => 'example.com',
			],
			'SAN available, multiple alternatives provided; none of the SANs match, DNS prefix missing in first, not second' => [
				'host'      => 'example.net',
				'reference' => 'example.com',
				'with_san'  => 'example.com, DNS: example.org',
			],
			'SAN available, multiple alternatives provided; none of the SANs match, even though CN does' => [
				'host'      => 'example.net',
				'reference' => 'example.net',
				'with_san'  => 'DNS: example.com, DNS: example.org, DNS: example.info',
			],
		];
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
		return [
			'empty array' => [
				'host'        => 'example.com',
				'certificate' => [],
				'expected'    => false,
			],
			'subject, but missing CN entry; no SAN' => [
				'host'        => 'example.com',
				'certificate' => [
					'subject' => [],
				],
				'expected'    => false,
			],
			'subject with empty CN entry; no SAN' => [
				'host'        => 'example.com',
				'certificate' => [
					'subject' => [
						'CN' => '',
					],
				],
				'expected'    => false,
			],
			'subject, but missing CN entry; SAN exists, missing DNS' => [
				'host'        => 'example.com',
				'certificate' => [
					'subject'    => [],
					'extensions' => [
						'subjectAltName' => 'example.net',
					],
				],
				'expected'    => false,
			],
			'subject with empty CN entry; SAN exists, missing DNS' => [
				'host'        => 'example.com',
				'certificate' => [
					'subject' => [
						'CN' => '',
					],
					'extensions' => [
						'subjectAltName' => 'example.net',
					],
				],
				'expected'    => false,
			],
			'subject, but missing CN entry; SAN exists, but non-matching' => [
				'host'        => 'example.com',
				'certificate' => [
					'subject'    => [],
					'extensions' => [
						'subjectAltName' => 'DNS: example.net',
					],
				],
				'expected'    => false,
			],
			'subject with empty CN entry; SAN exists, but non-matching' => [
				'host'        => 'example.com',
				'certificate' => [
					'subject' => [
						'CN' => '',
					],
					'extensions' => [
						'subjectAltName' => 'DNS:   example.net',
					],
				],
				'expected'    => false,
			],
			'extensions, but missing SAN entry' => [
				'host'        => 'example.com',
				'certificate' => [
					'subject'    => [
						'CN' => 'example.net',
					],
					'extensions' => [],
				],
				'expected'    => false,
			],
			'extensions with empty SAN entry' => [
				'host'        => 'example.com',
				'certificate' => [
					'subject' => [
						'CN' => 'example.net',
					],
					'extensions' => [
						'subjectAltName' => '',
					],
				],
				'expected'    => false,
			],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as $host.
	 *
	 * @dataProvider dataInvalidInputTypeStringable
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

		Ssl::verify_certificate($input, []);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as $cert.
	 *
	 * @dataProvider dataInvalidInputTypeArrayAccess
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
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInputTypeArrayAccess() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed.
	 *
	 * @dataProvider dataInvalidInputTypeStringable
	 *
	 * @covers ::match_domain
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testMatchDomainInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($host) must be of type string|Stringable');

		Ssl::match_domain($input, 'reference');
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInputTypeStringable() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRINGABLE);
	}
}
