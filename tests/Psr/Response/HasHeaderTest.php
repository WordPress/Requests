<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use InvalidArgumentException;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class HasHeaderTest extends TestCase {

	/**
	 * Tests receiving boolean when using hasHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::hasHeader
	 *
	 * @return void
	 */
	public function testHasHeaderReturnsFalse() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertFalse($response->hasHeader('name'));
	}

	/**
	 * Tests receiving boolean when using hasHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::hasHeader
	 *
	 * @return void
	 */
	public function testHasHeaderReturnsTrue() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));
		$response = $response->withHeader('name', 'value');

		$this->assertTrue($response->hasHeader('name'));
	}

	/**
	 * Tests receiving boolean when using hasHeader().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::hasHeader
	 *
	 * @return void
	 */
	public function testHasHeaderWithCaseInsensitiveNameReturnsTrue() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));
		$response = $response->withHeader('NAME', 'value');

		$this->assertTrue($response->hasHeader('name'));
	}

	/**
	 * Tests receiving an exception when the hasHeader() method received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Response::hasHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testHasHeaderWithoutStringThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::hasHeader(): Argument #1 ($name) must be of type string,', Response::class));

		$response->hasHeader($input);
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
