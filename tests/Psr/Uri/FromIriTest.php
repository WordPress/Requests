<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;

final class FromIriTest extends TestCase {

	/**
	 * Tests receiving an Uri instance when using fromIri().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::fromIri
	 *
	 * @return void
	 */
	public function testFromIriReturnsUri() {
		$this->assertInstanceOf(
			UriInterface::class,
			Uri::fromIri(new Iri('https://example.org'))
		);
	}
}
