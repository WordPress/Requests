<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class GetHeaderLineTest extends TestCase {

	/**
	 * Tests receiving the header when using getHeaderLine().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getHeaderLine
	 *
	 * @return void
	 */
	public function testGetHeaderLineWithoutHeaderReturnsEmptyString() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertSame('', $request->getHeaderLine('name'));
	}

	/**
	 * Tests receiving the header when using getHeaderLine().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getHeaderLine
	 *
	 * @return void
	 */
	public function testGetHeaderLineReturnsString() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));
		$request = $request->withHeader('name', ['value1', 'value2']);

		$this->assertSame('value1,value2', $request->getHeaderLine('name'));
	}

	/**
	 * Tests receiving the header when using getHeaderLine().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getHeaderLine
	 *
	 * @return void
	 */
	public function testGetHeaderLineWithCaseInsensitiveNameReturnsString() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));
		$request = $request->withHeader('name', 'value');

		$this->assertSame('value', $request->getHeaderLine('NAME'));
	}

	/**
	 * Tests receiving an exception when the getHeaderLine() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getHeaderLine
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testGetHeaderLineWithoutStringThrowsInvalidArgumentException($input) {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::getHeaderLine(): Argument #1 ($name) must be of type string,', Request::class));

		$request->getHeaderLine($input);
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
