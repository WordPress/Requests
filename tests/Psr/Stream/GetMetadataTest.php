<?php

namespace WpOrg\Requests\Tests\Psr\Stream;

use InvalidArgumentException;
use WpOrg\Requests\Psr\Stream;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class GetMetadataTest extends TestCase {

	/**
	 * Tests receiving an array when using getMetadata().
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::getMetadata
	 *
	 * @return void
	 */
	public function testGetMetadataReturnsArray() {
		$stream = Stream::createFromString('');

		$this->assertSame([], $stream->getMetadata());
	}

	/**
	 * Tests receiving null when using getMetadata().
	 *
	 * @covers \WpOrg\Requests\Psr\Stream::getMetadata
	 *
	 * @return void
	 */
	public function testGetMetadataWithKeyReturnsNull() {
		$stream = Stream::createFromString('');

		$this->assertNull($stream->getMetadata('key'));
	}

	/**
	 * Tests receiving an exception when the withHeader() method received an invalid input type as `$value`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withHeader
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testGetMetadataWithoutStringThrowsInvalidArgumentException($input) {
		$stream = Stream::createFromString('');

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::getMetadata(): Argument #1 ($key) must be of type string', Stream::class));

		$stream->getMetadata($input);
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
