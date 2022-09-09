<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithPortTest extends TestCase {

	/**
	 * Tests changing the port when using withPort().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withPort
	 *
	 * @return void
	 */
	public function testWithPortReturnsUri() {
		$uri = Uri::fromIri(new Iri('https://example.org'));

		$this->assertInstanceOf(UriInterface::class, $uri->withPort(5000));
	}

	/**
	 * Tests changing the port when using withPort().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withPort
	 *
	 * @return void
	 */
	public function testWithPortWithNullRemovesThePort() {
		$uri = Uri::fromIri(new Iri('https://example.org:5000'));

		$uri = $uri->withPort(null);

		$this->assertSame(null, $uri->getPort());
	}

	/**
	 * Tests changing the port when using withPort().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withPort
	 *
	 * @return void
	 */
	public function testWithPortReturnsNewInstance() {
		$uri = Uri::fromIri(new Iri('https://example.org'));

		$this->assertNotSame($uri, $uri->withPort(5000));
	}

	/**
	 * Tests receiving an exception when the withPort() method received an invalid input type as `$port`.
	 *
	 * @dataProvider dataInvalidTypeNotIntOrNull
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withPort
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithPortWithoutIntOrNullThrowsInvalidArgumentException($input) {
		$uri = Uri::fromIri(new Iri('https://example.org'));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withPort(): Argument #1 ($port) must be of type null|int', Uri::class));

		$uri = $uri->withPort($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotIntOrNull() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT, TypeProviderHelper::GROUP_NULL);
	}

	/**
	 * Tests changing the port when using withPort().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withPort
	 *
	 * @return void
	 */
	public function testWithPortChangesThePort() {
		$uri = Uri::fromIri(new Iri('https://example.org'));

		$uri = $uri->withPort(5000);

		$this->assertSame(5000, $uri->getPort());
	}

	/**
	 * Tests receiving an exception when the withPort() method received a port outside the
	 * established TCP and UDP port ranges as `$port`.
	 *
	 * @dataProvider dataInvalidPorts
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withPort
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithPortWithoutValidPortThrowsInvalidArgumentException($input) {
		$uri = Uri::fromIri(new Iri('https://example.org'));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withPort(): Argument #1 ($port) must be of type null|int in the range of 0 - 65535', Uri::class));

		$uri = $uri->withPort($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidPorts() {
		return [
			'negative integer' => [-1],
			'bigger than 65535' => [65536],
		];
	}
}
