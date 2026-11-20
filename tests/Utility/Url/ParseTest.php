<?php

namespace WpOrg\Requests\Tests\Utility\Url;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\Url;

/**
 * @covers \WpOrg\Requests\Utility\Url::parse
 * @covers \WpOrg\Requests\Utility\Url::is_requestable
 */
final class ParseTest extends TestCase {

	/**
	 * Test that when a $url parameter of an incorrect type gets passed, an exception gets thrown.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $input Input to pass to the function.
	 *
	 * @return void
	 */
	public function testInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($url) must be of type string|Stringable');

		Url::parse($input);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataInvalidInputType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Test that a URL which a request cannot be sent to is rejected.
	 *
	 * @dataProvider dataInvalidUrl
	 *
	 * @param string $input Input to pass to the function.
	 *
	 * @return void
	 */
	public function testInvalidUrl($input) {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid URL.');

		try {
			Url::parse($input);
		} catch (Exception $e) {
			$this->assertSame('invalidurl', $e->getType(), 'Exception type does not match');
			throw $e;
		}
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataInvalidUrl() {
		return [
			'empty string'         => [''],
			'no scheme, no host'   => ['example.com/path'],
			'path only'            => ['/path?query'],
			'scheme only'          => ['http://'],
			'empty host'           => ['http:///path'],
			'invalid IPv6 host'    => ['http://[zz]/'],
			'scheme without host'  => ['mailto:user@example.com'],
		];
	}

	/**
	 * Test that the parts of a URL are made available.
	 *
	 * @return void
	 */
	public function testParsesUrl() {
		$iri = Url::parse('HTTPS://user:pass@Example.COM:8080/a/./b/../c?x=1#frag');

		$this->assertSame('https', $iri->scheme, 'Scheme does not match');
		$this->assertSame('user:pass', $iri->userinfo, 'Userinfo does not match');
		$this->assertSame('example.com', $iri->host, 'Host does not match');
		$this->assertSame(8080, $iri->port, 'Port does not match');
		$this->assertSame('/a/c', $iri->path, 'Path does not match');
		$this->assertSame('x=1', $iri->query, 'Query does not match');
		$this->assertSame('frag', $iri->fragment, 'Fragment does not match');
	}

	/**
	 * Test that the default port for the scheme is used when the URL does not contain a port.
	 *
	 * @dataProvider dataDefaultPort
	 *
	 * @param string $input    Input to pass to the function.
	 * @param int    $expected Expected port.
	 *
	 * @return void
	 */
	public function testDefaultPort($input, $expected) {
		$this->assertSame($expected, Url::parse($input)->port);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataDefaultPort() {
		return [
			'http'                    => ['http://example.com/', 80],
			'https'                   => ['https://example.com/', 443],
			'http, default port'      => ['http://example.com:80/', 80],
			'https, non-default port' => ['https://example.com:8443/', 8443],
		];
	}

	/**
	 * Test that a URL without a path gets "/" as the path.
	 *
	 * @return void
	 */
	public function testUrlWithoutPathGetsRootPath() {
		$this->assertSame('/', Url::parse('http://example.com?x=1')->path);
	}

	/**
	 * Test that a Stringable object is accepted.
	 *
	 * @return void
	 */
	public function testAcceptsStringableObject() {
		$iri = Url::parse(new StringableObject('http://example.com/path'));

		$this->assertSame('http://example.com/path', $iri->uri);
	}

	/**
	 * Test that a passed Iri object is not modified when the returned object is changed.
	 *
	 * @return void
	 */
	public function testDoesNotModifyPassedIri() {
		$original = new Iri('http://example.com/path?x=1');

		$iri        = Url::parse($original);
		$iri->query = 'changed';

		$this->assertNotSame($original, $iri, 'A new object should be returned');
		$this->assertSame('http://example.com/path?x=1', $original->uri, 'The passed object should not be modified');
	}
}
