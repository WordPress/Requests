<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithAddedHeaderTest extends TestCase {

	/**
	 * Tests changing the header when using withAddedHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withAddedHeader
	 *
	 * @return void
	 */
	public function testWithAddedHeaderReturnsResponseInterface() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertInstanceOf(ResponseInterface::class, $response->withAddedHeader('name', 'value'));
	}

	/**
	 * Tests changing the header when using withAddedHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withAddedHeader
	 *
	 * @return void
	 */
	public function testWithAddedHeaderReturnsNewInstance() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertNotSame($response, $response->withAddedHeader('name', 'value'));
	}

	/**
	 * Tests receiving an exception when the withAddedHeader() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withAddedHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithAddedHeaderWithoutNameAsStringThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withAddedHeader(): Argument #1 ($name) must be of type string', Response::class));

		$response->withAddedHeader($input, 'value');
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
	 * @covers \WpOrg\Requests\Psr\Response::withAddedHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithAddedHeaderWithoutValueAsStringOrArrayThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withAddedHeader(): Argument #2 ($value) must be of type string|array', Response::class));

		$response->withAddedHeader('name', $input);
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
	 * @covers \WpOrg\Requests\Psr\Response::withAddedHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithAddedHeaderWithoutValueAsStringInArrayThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withAddedHeader(): Argument #2 ($value) must be of type string|array containing strings', Response::class));

		$response->withAddedHeader('name', [$input]);
	}

	/**
	 * Tests changing the header when using withAddedHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withAddedHeader
	 *
	 * @return void
	 */
	public function testWithAddedHeaderChangesTheHeaders() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$response = $response->withAddedHeader('Name', 'value');

		$this->assertSame(['Name' => ['value']], $response->getHeaders());
	}

	/**
	 * Tests changing the header when using withAddedHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withAddedHeader
	 *
	 * @return void
	 */
	public function testWithAddedHeaderCaseInsensitiveChangesTheHeaders() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$response = $response->withAddedHeader('name', 'value1');
		$response = $response->withAddedHeader('NAME', 'value2');

		$this->assertSame(['NAME' => ['value1', 'value2']], $response->getHeaders());
	}
}
