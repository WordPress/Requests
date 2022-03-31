<?php

namespace WpOrg\Requests\Tests\Exception\ArgumentCount;

use WpOrg\Requests\Exception\ArgumentCount;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Exception\ArgumentCount
 */
final class ArgumentCountTest extends TestCase {

	/**
	 * Test that the text of the exception is as expected.
	 *
	 * @return void
	 */
	public function testCreate() {
		$this->expectException(ArgumentCount::class);
		$this->expectExceptionMessage('ArgumentCountTest::testCreate() expects exactly 1 argument, 0 given');

		throw ArgumentCount::create('exactly 1 argument', 0, 'code');
	}
}
