<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithHeaderTest extends TestCase {

	/**
	 * Tests changing the header when using withHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withHeader
	 *
	 * @return void
	 */
	public function testWithHeaderReturnsRequest() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertInstanceOf(RequestInterface::class, $request->withHeader('name', 'value'));
	}

	/**
	 * Tests changing the header when using withHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withHeader
	 *
	 * @return void
	 */
	public function testWithHeaderReturnsNewInstance() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertNotSame($request, $request->withHeader('name', 'value'));
	}

	/**
	 * Tests receiving an exception when the withHeader() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithHeaderWithoutNameAsStringThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withHeader(): Argument #1 ($name) must be of type string', Request::class));

		$request->withHeader($input, 'value');
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
	 * Tests receiving an exception when the withHeader() method received an invalid input type as `$value`.
	 *
	 * @dataProvider dataInvalidTypeNotStringOrArray
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithHeaderWithoutValueAsStringOrArrayThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withHeader(): Argument #2 ($value) must be of type string|array', Request::class));

		$request->withHeader('name', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotStringOrArray() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING, TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Tests receiving an exception when the withHeader() method received an invalid input type as `$value`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithHeaderWithoutValueAsStringInArrayThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withHeader(): Argument #2 ($value) must be of type string|array containing strings', Request::class));

		$request->withHeader('name', [$input]);
	}

	/**
	 * Tests changing the header when using withHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withHeader
	 *
	 * @return void
	 */
	public function testWithHeaderChangesTheHeaders() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');
		$request = Request::withMethodAndUri('GET', $uri);

		$request = $request->withHeader('Name', 'value');

		$this->assertSame(['Name' => ['value']], $request->getHeaders());
	}

	/**
	 * Tests changing the header when using withHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withHeader
	 *
	 * @return void
	 */
	public function testWithHeaderCaseInsensitiveChangesTheHeaders() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');
		$request = Request::withMethodAndUri('GET', $uri);

		$request = $request->withHeader('name', 'value');
		$request = $request->withHeader('NAME', 'value');

		$this->assertSame(['NAME' => ['value']], $request->getHeaders());
	}
}
