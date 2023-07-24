<?php

namespace WpOrg\Requests\Tests\Ssl;

use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;

abstract class SslTestCase extends TestCase {

	/**
	 * Test helper to mock a certificate.
	 *
	 * @param string      $dnsname  DNS name to match against.
	 * @param bool|string $with_san Optional. How to generate the fake certificate.
	 *                              - false:  plain, CN only;
	 *                              - true:   CN + subjectAltName, alt set to same as CN;
	 *                              - string: CN + subjectAltName, alt set to string value.
	 *
	 * @return array
	 */
	protected function fakeCertificate($dnsname, $with_san = true) {
		$certificate = [
			'subject' => [
				'CN' => $dnsname,
			],
		];

		if ($with_san !== false) {
			// If SAN is set to true, default it to the dNSName.
			if ($with_san === true) {
				$with_san = 'DNS: ' . $dnsname;
			}

			$certificate['extensions'] = [
				'subjectAltName' => $with_san,
			];
		}

		return $certificate;
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataMatch() {
		return [
			'top-level domain (stringable object)' => [
				'host'      => new StringableObject('example.com'),
				'reference' => new StringableObject('example.com'),
			],
			'subdomain' => [
				'host'      => 'test.example.com',
				'reference' => 'test.example.com',
			],
			'subdomain with wildcard reference' => [
				'host'      => 'test.example.com',
				'reference' => '*.example.com',
			],
		];
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataNoMatch() {
		return [
			// Check that we need at least 3 components.
			'not a domain; wildcard reference' => [
				'host'      => 'com',
				'reference' => '*',
			],
			'domain name; wildcard on TLD as reference' => [
				'host'      => 'example.com',
				'reference' => '*.com',
			],

			// Check that double wildcards don't work.
			'domain name; double wildcard in reference' => [
				'host'      => 'abc.def.example.com',
				'reference' => '*.*.example.com',
			],

			// Check that we only match with the correct number of components.
			'four-level domain; three-level reference' => [
				'host'      => 'abc.def.example.com',
				'reference' => 'def.example.com',
			],
			'four-level domain; three-level wildcard reference' => [
				'host'      => 'abc.def.example.com',
				'reference' => '*.example.com',
			],

			// Check that the wildcard only works as the full first component.
			'four-level domain; four-level reference, but wildcard not stand-alone' => [
				'host'      => 'abc.def.example.com',
				'reference' => 'a*.def.example.com',
			],

			// Check that wildcards are not allowed for IPs.
			'IP address; wildcard in reference (start)' => [
				'host'      => '192.168.0.1',
				'reference' => '*.168.0.1',
			],
			'IP address; wildcard in reference (end)' => [
				'host'      => '192.168.0.1',
				'reference' => '192.168.0.*',
			],

			// IP vs named address.
			'IP address vs named address' => [
				'host'      => '192.168.0.1',
				'reference' => '*.example.com',
			],
			'Named address vs IP address' => [
				'host'      => 'example.com',
				'reference' => '192.168.0.1',
			],
		];
	}
}
