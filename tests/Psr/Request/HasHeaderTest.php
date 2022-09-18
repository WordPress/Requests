<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class HasHeaderTest extends TestCase {

	/**
	 * Tests receiving boolean when using hasHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::hasHeader
	 *
	 * @return void
	 */
	public function testHasHeaderReturnsFalse() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertFalse($request->hasHeader('name'));
	}

	/**
	 * Tests receiving boolean when using hasHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::hasHeader
	 *
	 * @return void
	 */
	public function testHasHeaderReturnsTrue() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));
		$request = $request->withHeader('name', 'value');

		$this->assertTrue($request->hasHeader('name'));
	}

	/**
	 * Tests receiving boolean when using hasHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::hasHeader
	 *
	 * @return void
	 */
	public function testHasHeaderWithCaseInsensitiveNameReturnsTrue() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));
		$request = $request->withHeader('NAME', 'value');

		$this->assertTrue($request->hasHeader('name'));
	}

	/**
	 * Tests receiving an exception when the hasHeader() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::hasHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testHasHeaderWithoutStringThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::hasHeader(): Argument #1 ($name) must be of type string,', Request::class));

		$request->hasHeader($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotString() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}
}
