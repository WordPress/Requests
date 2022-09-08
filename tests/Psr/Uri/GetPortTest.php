<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;

final class GetPortTest extends TestCase {

	/**
	 * Tests receiving the port when using getPort().
	 *
	 * @dataProvider dataGetPort
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::getPort
	 *
	 * @return void
	 */
	public function testGetPort($input, $expected) {
		$uri = Uri::fromIri(new Iri($input));

		$this->assertSame($expected, $uri->getPort());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataGetPort() {
		return [
			'retrieve the port component of the URI' => ['https://example.org:12345', 12345],
			'port is present, and it is non-standard for the current scheme, return integer' => ['http://example.org:443', 443],
			'port is the standard port used with the current scheme, return null' => ['https://example.org:443', null],
			'no port is present, and no scheme is present, return null' => ['example.org', null],
			'no port is present, but a scheme is present, SHOULD return null' => ['https://example.org', null],
			'empty' => ['', null],
		];
	}
}
