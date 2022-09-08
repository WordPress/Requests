<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;

final class GetQueryTest extends TestCase {

	/**
	 * Tests receiving the query when using getQuery().
	 *
	 * @dataProvider dataGetQuery
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::getQuery
	 *
	 * @return void
	 */
	public function testGetQuery($input, $expected) {
		$uri = Uri::fromIri(new Iri($input));

		$this->assertSame($expected, $uri->getQuery());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataGetQuery() {
		return [
			'empty' => ['', ''],
			'Retrieve the query string of the URI' => ['https://example.com?foo=bar', 'foo=bar'],
			'If no query string is present, return empty string' => ['https://example.com', ''],
			'The leading "?" character is not part of the query' => ['https://example.com?', ''],
			'The value returned MUST be percent-encoded' => ['https://example.com?foo=%26', 'foo=%26'],
			'ampersand ("&") must be passed in encoded form' => ['https://example.com?foo=%26&fo=ba', 'foo=%26&fo=ba'],
		];
	}
}
