<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use InvalidArgumentException;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class GetHeaderTest extends TestCase {

	/**
	 * Tests receiving the header when using getHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeader
	 *
	 * @return void
	 */
	public function testGetHeaderWithoutHeaderReturnsEmptyArray() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertSame([], $response->getHeader('name'));
	}

	/**
	 * Tests receiving the header when using getHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeader
	 *
	 * @return void
	 */
	public function testGetHeaderReturnsArray() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));
		$response = $response->withHeader('name', 'value');

		$this->assertSame(['value'], $response->getHeader('name'));
	}

	/**
	 * Tests receiving the header when using getHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeader
	 *
	 * @return void
	 */
	public function testGetHeaderWithCaseInsensitiveNameReturnsArray() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));
		$response = $response->withHeader('name', 'value');

		$this->assertSame(['value'], $response->getHeader('NAME'));
	}

	/**
	 * Tests receiving an exception when the getHeader() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testGetHeaderWithoutStringThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::getHeader(): Argument #1 ($name) must be of type string,', Response::class));

		$response->getHeader($input);
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
