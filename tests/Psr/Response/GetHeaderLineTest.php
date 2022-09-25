<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use InvalidArgumentException;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class GetHeaderLineTest extends TestCase {

	/**
	 * Tests receiving the header when using getHeaderLine().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeaderLine
	 *
	 * @return void
	 */
	public function testGetHeaderLineWithoutHeaderReturnsEmptyString() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertSame('', $response->getHeaderLine('name'));
	}

	/**
	 * Tests receiving the header when using getHeaderLine().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeaderLine
	 *
	 * @return void
	 */
	public function testGetHeaderLineReturnsString() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));
		$response = $response->withHeader('name', ['value1', 'value2']);

		$this->assertSame('value1,value2', $response->getHeaderLine('name'));
	}

	/**
	 * Tests receiving the header when using getHeaderLine().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeaderLine
	 *
	 * @return void
	 */
	public function testGetHeaderLineWithCaseInsensitiveNameReturnsString() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));
		$response = $response->withHeader('name', 'value');

		$this->assertSame('value', $response->getHeaderLine('NAME'));
	}

	/**
	 * Tests receiving an exception when the getHeaderLine() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Response::getHeaderLine
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testGetHeaderLineWithoutStringThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::getHeaderLine(): Argument #1 ($name) must be of type string,', Response::class));

		$response->getHeaderLine($input);
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
