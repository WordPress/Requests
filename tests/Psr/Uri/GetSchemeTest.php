<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;

final class GetSchemeTest extends TestCase {

	/**
	 * Tests receiving the scheme when using getScheme().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::getScheme
	 *
	 * @return void
	 */
	public function testGetScheme() {
		$uri = Uri::fromIri(new Iri('https://example.org'));

		$this->assertSame('https', $uri->getScheme());
	}

	/**
	 * Tests receiving the scheme when using getScheme().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::getScheme
	 *
	 * @return void
	 */
	public function testGetSchemeReturnEmptyString() {
		$uri = Uri::fromIri(new Iri('example.org'));

		$this->assertSame('', $uri->getScheme());
	}
}
