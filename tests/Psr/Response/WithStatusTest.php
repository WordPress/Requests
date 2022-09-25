<?php

namespace WpOrg\Requests\Tests\Psr\Response;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use WpOrg\Requests\Psr\Response;
use WpOrg\Requests\Response as RequestsResponse;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithStatusTest extends TestCase {

	/**
	 * Tests changing the status code when using withStatus().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withStatus
	 *
	 * @return void
	 */
	public function testWithStatusReturnsResponseInstance() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertInstanceOf(ResponseInterface::class, $response->withStatus(200));
	}

	/**
	 * Tests changing the status code when using withStatus().
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withStatus
	 *
	 * @return void
	 */
	public function testWithStatusReturnsNewInstance() {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->assertNotSame($response, $response->withStatus(200));
	}

	/**
	 * Tests receiving an exception when the withStatus() method received an invalid input type as `$code`.
	 *
	 * @dataProvider dataInvalidTypeNotInteger
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withStatus
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithStatusWithoutIntInCodeThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withStatus(): Argument #1 ($code) must be of type int, ', Response::class));

		$response = $response->withStatus($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotInteger() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT);
	}

	/**
	 * Tests receiving an exception when the withStatus() method received an invalid input type as `$reasonPhrase`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withStatus
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithStatusWithoutStringInReasonPhraseThrowsInvalidArgumentException($input) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withStatus(): Argument #2 ($reasonPhrase) must be of type string, ', Response::class));

		$response = $response->withStatus(200, $input);
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
	 * Tests receiving an exception when the withStatus() method received an invalid input type as `$reasonPhrase`.
	 *
	 * @dataProvider dataWithStatus
	 *
	 * @covers \WpOrg\Requests\Psr\Response::withStatus
	 *
	 * @param int $code
	 * @param string $phrase
	 * @param string $expected
	 *
	 * @return void
	 */
	public function testWithStatusChangesStatusCode($code, $phrase, $expected) {
		$response = Response::fromResponse($this->createMock(RequestsResponse::class));

		$response = $response->withStatus($code, $phrase);

		$this->assertSame($code, $response->getStatusCode());
		$this->assertSame($expected, $response->getReasonPhrase());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataWithStatus() {
		return [
			'Return an instance with the specified status code and, optionally, reason phrase.' => [200, 'foobar', 'foobar'],
			'If no reason phrase is specified, implementations MAY choose to default to the RFC 7231 or IANA recommended reason phrase' => [200, '', 'OK'],
		];
	}
}
