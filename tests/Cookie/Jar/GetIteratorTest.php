<?php

namespace WpOrg\Requests\Tests\Cookie\Jar;

use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Cookie\Jar::getIterator
 */
final class GetIteratorTest extends TestCase {

	public function testJarIterator() {
		$cookies = [
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		];
		$jar     = new Jar($cookies);

		foreach ($jar as $key => $value) {
			$this->assertSame($cookies[$key], $value, "Value for $key does not match expectation during iteration");
		}
	}
}
