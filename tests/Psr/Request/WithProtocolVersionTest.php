<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithProtocolVersionTest extends TestCase {

	/**
	 * Tests changing the version when using withProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withProtocolVersion
	 *
	 * @return void
	 */
	public function testWithProtocolVersionReturnsRequest() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertInstanceOf(RequestInterface::class, $request->withProtocolVersion('1.0'));
	}

	/**
	 * Tests changing the version when using withProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withProtocolVersion
	 *
	 * @return void
	 */
	public function testWithProtocolVersionReturnsNewInstance() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertNotSame($request, $request->withProtocolVersion('1.0'));
	}

	/**
	 * Tests receiving an exception when the withProtocolVersion() method received an invalid input type as `$method`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withProtocolVersion
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithProtocolVersionWithoutStringThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withProtocolVersion(): Argument #1 ($version) must be of type string', Request::class));

		$request->withProtocolVersion($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotString() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Tests changing the version when using withProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withProtocolVersion
	 *
	 * @return void
	 */
	public function testWithProtocolVersionChangesTheProtocolVersion() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$request = $request->withProtocolVersion('1.0');

		$this->assertSame('1.0', $request->getProtocolVersion());
	}
}
