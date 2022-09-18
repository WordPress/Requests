<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithRequestTargetTest extends TestCase {

	/**
	 * Tests changing the request-target when using withRequestTarget().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withRequestTarget
	 *
	 * @return void
	 */
	public function testWithRequestTargetReturnsRequest() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertInstanceOf(RequestInterface::class, $request->withRequestTarget('/'));
	}

	/**
	 * Tests changing the request-target when using withRequestTarget().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withRequestTarget
	 *
	 * @return void
	 */
	public function testWithRequestTargetReturnsNewInstance() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertNotSame($request, $request->withRequestTarget('/'));
	}

	/**
	 * Tests receiving an exception when the withRequestTarget() method received an invalid input type as `$requestTarget`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withRequestTarget
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithRequestTargetWithoutStringThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withRequestTarget(): Argument #1 ($requestTarget) must be of type string', Request::class));

		$request->withRequestTarget($input);
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
	 * Tests changing the request-target when using withRequestTarget().
	 *
	 * @dataProvider dataValidRequestTarget
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withRequestTarget
	 *
	 * @param string $input
	 * @param string $expected
	 *
	 * @return void
	 */
	public function testWithRequestTargetChangesTheRequestTarget($input, $expected) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$request = $request->withRequestTarget($input);

		$this->assertSame($expected, $request->getRequestTarget());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataValidRequestTarget() {
		return [
			'Return an instance with the specific request-target' => ['path', 'path'],
			'Return an instance with origin-form' => ['absolute-path?query', 'absolute-path?query'],
			'Return an instance with absolute-form' => ['http://www.example.org/pub/WWW/TheProject.html', 'http://www.example.org/pub/WWW/TheProject.html'],
			'Return an instance with authority-form' => ['www.example.com:80', 'www.example.com:80'],
			'Return an instance with asterisk-form' => ['*', '*'],
			'If no request-target has been specifically provided, this method MUST return the string "/".' => ['', '/'],
		];
	}
}
