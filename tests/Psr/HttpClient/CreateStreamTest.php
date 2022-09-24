<?php

namespace WpOrg\Requests\Tests\Psr\HttpClient;

use Psr\Http\Message\StreamInterface;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Psr\HttpClient;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class CreateStreamTest extends TestCase {

	/**
	 * Tests receiving a Stream when using createStream().
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::createStream
	 *
	 * @return void
	 */
	public function testCreateStreamReturnsStream() {
		$httpClient = new HttpClient();

		$this->assertInstanceOf(
			StreamInterface::class,
			$httpClient->createStream('')
		);
	}

	/**
	 * Tests receiving an exception when the createStream() method received an invalid input type as `$content`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\HttpClient::createStream
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testCreateStreamWithoutStringThrowsException($input) {
		$httpClient = new HttpClient();

		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage(sprintf(
			'%s::createStream(): Argument #1 ($content) must be of type string,',
			HttpClient::class
		));

		$httpClient->createStream($input);
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
