<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithProtocolVersionTest extends TestCase {

	/**
	 * Tests changing the protocol version when using withProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withProtocolVersion
	 *
	 * @return void
	 */
	public function testWithProtocolVersionReturnsResponse() {
		$requestsResponse = new RequestsResponse();
		$response = Response::fromResponse($requestsResponse);

		$this->assertInstanceOf(ResponseInterface::class, $response->withProtocolVersion('1.0'));
	}

	/**
	 * Tests changing the protocol version when using withProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withProtocolVersion
	 *
	 * @return void
	 */
	public function testWithProtocolVersionReturnsNewInstance() {
		$requestsResponse = new RequestsResponse();
		$response = Response::fromResponse($requestsResponse);

		$this->assertNotSame($response, $response->withProtocolVersion('1.0'));
	}

	/**
	 * Tests receiving an exception when the withProtocolVersion() method received an invalid input type as `$method`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withProtocolVersion
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithProtocolVersionWithoutStringThrowsInvalidArgumentException($input) {
		$requestsResponse = new RequestsResponse();
		$response = Response::fromResponse($requestsResponse);

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withProtocolVersion(): Argument #1 ($version) must be of type string, ', Response::class));

		$response->withProtocolVersion($input);
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
	 * Tests changing the version when using withProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withProtocolVersion
	 *
	 * @return void
	 */
	public function testWithProtocolVersionChangesTheProtocolVersion() {
		$requestsResponse = new RequestsResponse();
		$response = Response::fromResponse($requestsResponse);

		$response = $response->withProtocolVersion('1.0');

		$this->assertSame('1.0', $response->getProtocolVersion());
	}
}
