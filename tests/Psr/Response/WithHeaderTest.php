<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithHeaderTest extends TestCase {

	/**
	 * Tests changing the header when using withHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withHeader
	 *
	 * @return void
	 */
	public function testWithHeaderReturnsResponseInterface() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertInstanceOf(ResponseInterface::class, $response->withHeader('name', 'value'));
	}

	/**
	 * Tests changing the header when using withHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withHeader
	 *
	 * @return void
	 */
	public function testWithHeaderReturnsNewInstance() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertNotSame($response, $response->withHeader('name', 'value'));
	}

	/**
	 * Tests receiving an exception when the withHeader() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithHeaderWithoutNameAsStringThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withHeader(): Argument #1 ($name) must be of type string', Response::class));

		$response->withHeader($input, 'value');
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
	 * @covers \WpOrg\Requests\Psr\Response::withHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithHeaderWithoutValueAsStringOrArrayThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withHeader(): Argument #2 ($value) must be of type string|array', Response::class));

		$response->withHeader('name', $input);
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
	 * @covers \WpOrg\Requests\Psr\Response::withHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithHeaderWithoutValueAsStringInArrayThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withHeader(): Argument #2 ($value) must be of type string|array containing strings', Response::class));

		$response->withHeader('name', [$input]);
	}

	/**
	 * Tests changing the header when using withHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withHeader
	 *
	 * @return void
	 */
	public function testWithHeaderChangesTheHeaders() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$response = $response->withHeader('Name', 'value');

		$this->assertSame(['Name' => ['value']], $response->getHeaders());
	}

	/**
	 * Tests changing the header when using withHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withHeader
	 *
	 * @return void
	 */
	public function testWithHeaderCaseInsensitiveChangesTheHeaders() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$response = $response->withHeader('name', 'value');
		$response = $response->withHeader('NAME', 'value');

		$this->assertSame(['NAME' => ['value']], $response->getHeaders());
	}
}
