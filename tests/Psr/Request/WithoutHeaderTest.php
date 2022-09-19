<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithoutHeaderTest extends TestCase {

	/**
	 * Tests removing the header when using withoutHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withoutHeader
	 *
	 * @return void
	 */
	public function testWithoutHeaderReturnsRequest() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertInstanceOf(RequestInterface::class, $request->withoutHeader('name'));
	}

	/**
	 * Tests removing the header when using withoutHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withoutHeader
	 *
	 * @return void
	 */
	public function testWithoutHeaderReturnsNewInstance() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertNotSame($request, $request->withoutHeader('name'));
	}

	/**
	 * Tests receiving an exception when the withoutHeader() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withoutHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithoutHeaderWithoutNameAsStringThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withoutHeader(): Argument #1 ($name) must be of type string', Request::class));

		$request->withoutHeader($input, 'value');
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
	 * Tests removing the header when using withoutHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withoutHeader
	 *
	 * @return void
	 */
	public function testWithoutHeaderChangesTheHeaders() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');
		$request = Request::withMethodAndUri('GET', $uri);

		$request = $request->withHeader('Name', 'value');
		$request = $request->withoutHeader('Name');

		$this->assertSame([], $request->getHeaders());
	}

	/**
	 * Tests removing the header when using withoutHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withoutHeader
	 *
	 * @return void
	 */
	public function testWithoutHeaderCaseInsensitiveChangesTheHeaders() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');
		$request = Request::withMethodAndUri('GET', $uri);

		$request = $request->withHeader('NAME1', 'value1');
		$request = $request->withHeader('NAME2', 'value2');
		$request = $request->withoutHeader('name1');

		$this->assertSame(['NAME2' => ['value2']], $request->getHeaders());
	}
}
