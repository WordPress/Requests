<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;

final class GetFragmentTest extends TestCase {

	/**
	 * Tests receiving the fragment when using getFragment().
	 *
	 * @dataProvider dataGetFragment
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::getFragment
	 *
	 * @return void
	 */
	public function testGetFragment($input, $expected) {
		$uri = Uri::fromIri(new Iri($input));

		$this->assertSame($expected, $uri->getFragment());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataGetFragment() {
		return [
			'empty' => ['', ''],
			'Retrieve the fragment component of the URI' => ['https://example.org#fragment', 'fragment'],
			'If no fragment is present, return an empty string' => ['https://example.org', ''],
			'The leading "#" character is not part of the fragment' => ['https://example.org#', ''],
			'The value returned MUST be percent-encoded' => ['#fragment[]', 'fragment%5B%5D'],
		];
	}
}
