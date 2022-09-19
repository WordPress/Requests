<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use Psr\Http\Message\StreamInterface;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class CreateFromStringTest extends TestCase {

	/**
	 * Tests receiving the stream when using createFromString().
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::createFromString
	 *
	 * @return void
	 */
	public function testCreateFromStringReturnsStream() {
		$this->assertInstanceOf(
			StreamInterface::class,
			Stream::createFromString('')
		);
	}

	/**
	 * Tests receiving an exception when the createFromString() method received an invalid input type as `$method`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::createFromString
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testCreateFromStringWithoutStringThrowsException($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage(sprintf('%s::createFromString(): Argument #1 ($content) must be of type string, ', Stream::class));

		Stream::createFromString($input);
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
