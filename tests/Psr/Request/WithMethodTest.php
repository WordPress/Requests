<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithMethodTest extends TestCase {

	/**
	 * Tests changing the method when using withMethod().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withMethod
	 *
	 * @return void
	 */
	public function testWithMethodReturnsRequest() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertInstanceOf(RequestInterface::class, $request->withMethod('GET'));
	}

	/**
	 * Tests changing the method when using withMethod().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withMethod
	 *
	 * @return void
	 */
	public function testWithMethodReturnsNewInstance() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertNotSame($request, $request->withMethod('GET'));
	}

	/**
	 * Tests receiving an exception when the withMethod() method received an invalid input type as `$method`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withMethod
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithMethodWithoutStringThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withMethod(): Argument #1 ($method) must be of type string', Request::class));

		$request->withMethod($input);
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
	 * Tests changing the method when using withMethod().
	 *
	 * @dataProvider dataValidMethod
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withMethod
	 *
	 * @param string $input
	 * @param string $expected
	 *
	 * @return void
	 */
	public function testWithMethodChangesTheMethod($input, $expected) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$request = $request->withMethod($input);

		$this->assertSame($expected, $request->getMethod());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataValidMethod() {
		return [
			'Return an instance with the provided HTTP method' => ['POST', 'POST'],
			'implementations SHOULD NOT modify the given string' => ['Head', 'Head'],
			'do not throw InvalidArgumentException for invalid HTTP methods' => ['foobar', 'foobar'],
			'do not throw InvalidArgumentException for empty methods' => ['', ''],
		];
	}
}
