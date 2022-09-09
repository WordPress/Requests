<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;

final class ToStringTest extends TestCase {

	/**
	 * Tests if a scheme is present, it MUST be suffixed by ":" when using __toString().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::__toString
	 *
	 * @return void
	 */
	public function testToStringIfSchemeIsPresent() {
		$uri = Uri::fromIri(new Iri(''));
		$uri = $uri->withScheme('http');

		$this->assertSame('http:', $uri->__toString());
	}

	/**
	 * Tests if an authority is present, it MUST be prefixed by "//" when using __toString().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::__toString
	 *
	 * @return void
	 */
	public function testToStringIfAuthorityIsPresent() {
		$uri = Uri::fromIri(new Iri(''));
		$uri = $uri->withHost('host');

		$this->assertSame('//host/', $uri->__toString());
	}

	/**
	 * Tests the path can be concatenated without delimiters when using __toString().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::__toString
	 *
	 * @return void
	 */
	public function testToStringThePathIsConcatenated() {
		$uri = Uri::fromIri(new Iri(''));
		$uri = $uri->withPath('path');

		$this->assertSame('path', $uri->__toString());
	}

	/**
	 * Tests if the path is rootless and an authority is present when using __toString().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::__toString
	 *
	 * @return void
	 */
	public function testToStringIfThePathIsRootslessAndAuthorityIsPresent() {
		$uri = Uri::fromIri(new Iri(''));
		$uri = $uri->withPath('rootlesspath');
		$uri = $uri->withHost('host');

		$this->assertSame('//host/rootlesspath', $uri->__toString());
	}

	/**
	 * Tests if the path is starting with more than one "/" and no authority is present when using __toString().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::__toString
	 *
	 * @return void
	 */
	public function testToStringIfThePathIsStartingWithMultibleSlashesAndNoAuthorityIsPresent() {
		$uri = Uri::fromIri(new Iri(''));
		$uri = $uri->withPath('//path');

		$this->assertSame('/path', $uri->__toString());
	}

	/**
	 * Tests if a query is present, it MUST be prefixed by "?" when using __toString().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::__toString
	 *
	 * @return void
	 */
	public function testToStringIfQueryIsPresent() {
		$uri = Uri::fromIri(new Iri(''));
		$uri = $uri->withQuery('foo=bar');

		$this->assertSame('?foo=bar', $uri->__toString());
	}

	/**
	 * Tests if a fragment is present, it MUST be prefixed by "#" when using __toString().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::__toString
	 *
	 * @return void
	 */
	public function testToStringIfFragmentIsPresent() {
		$uri = Uri::fromIri(new Iri(''));
		$uri = $uri->withFragment('fragment');

		$this->assertSame('#fragment', $uri->__toString());
	}

	/**
	 * Tests the result when using __toString().
	 *
	 * @dataProvider dataToString
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::__toString
	 *
	 * @param string $input
	 * @param string $expected
	 *
	 * @return void
	 */
	public function testToStringReturnsCorrectString($input, $expected) {
		$uri = Uri::fromIri(new Iri($input));

		$this->assertSame($expected, $uri->__toString());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataToString() {
		return [
			'empty' => ['', ''],
			'only root path' => ['/', '/'],
			'with suffixed "/"' => ['http://host', 'http://host/'],
			'with authority' => ['http://user@host', 'http://user@host/'],
		];
	}
}
