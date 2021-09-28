<?php

namespace WpOrg\Requests\Tests\Exception;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Exception\InvalidArgument
 */
final class InvalidArgumentTest extends TestCase {

	/**
	 * Test that the text of the exception is as expected.
	 *
	 * @return void
	 */
	public function testCreate() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('InvalidArgumentTest::testCreate(): Argument #2 ($dummy) must be of type int|null, string given');

		throw InvalidArgument::create(2, '$dummy', 'int|null', 'string');
	}
}
