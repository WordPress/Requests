<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithAddedHeaderTest extends TestCase {

	/**
	 * Tests changing the header when using withAddedHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withAddedHeader
	 *
	 * @return void
	 */
	public function testWithAddedHeaderReturnsRequest() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertInstanceOf(RequestInterface::class, $request->withAddedHeader('name', 'value'));
	}

	/**
	 * Tests changing the header when using withAddedHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withAddedHeader
	 *
	 * @return void
	 */
	public function testWithAddedHeaderReturnsNewInstance() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertNotSame($request, $request->withAddedHeader('name', 'value'));
	}

	/**
	 * Tests receiving an exception when the withAddedHeader() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withAddedHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithAddedHeaderWithoutNameAsStringThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withAddedHeader(): Argument #1 ($name) must be of type string', Request::class));

		$request->withAddedHeader($input, 'value');
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
	 * Tests receiving an exception when the withAddedHeader() method received an invalid input type as `$value`.
	 *
	 * @dataProvider dataInvalidTypeNotStringOrArray
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withAddedHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithAddedHeaderWithoutValueAsStringOrArrayThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withAddedHeader(): Argument #2 ($value) must be of type string|array', Request::class));

		$request->withAddedHeader('name', $input);
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
	 * Tests receiving an exception when the withAddedHeader() method received an invalid input type as `$value`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withAddedHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithAddedHeaderWithoutValueAsStringInArrayThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withAddedHeader(): Argument #2 ($value) must be of type string|array containing strings', Request::class));

		$request->withAddedHeader('name', [$input]);
	}

	/**
	 * Tests changing the header when using withAddedHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withAddedHeader
	 *
	 * @return void
	 */
	public function testWithAddedHeaderChangesTheHeaders() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');
		$request = Request::withMethodAndUri('GET', $uri);

		$request = $request->withAddedHeader('Name', 'value');

		$this->assertSame(['Name' => ['value']], $request->getHeaders());
	}

	/**
	 * Tests changing the header when using withAddedHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withAddedHeader
	 *
	 * @return void
	 */
	public function testWithAddedHeaderCaseInsensitiveChangesTheHeaders() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');
		$request = Request::withMethodAndUri('GET', $uri);

		$request = $request->withAddedHeader('name', 'value1');
		$request = $request->withAddedHeader('NAME', 'value2');

		$this->assertSame(['NAME' => ['value1', 'value2']], $request->getHeaders());
	}
}
