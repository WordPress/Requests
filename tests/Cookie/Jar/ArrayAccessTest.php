<?php

namespace WpOrg\Requests\Tests\Cookie\Jar;

use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Cookie\Jar::offsetExists
 * @covers \WpOrg\Requests\Cookie\Jar::offsetGet
 * @covers \WpOrg\Requests\Cookie\Jar::offsetSet
 * @covers \WpOrg\Requests\Cookie\Jar::offsetUnset
 */
final class ArrayAccessTest extends TestCase {

	public function testJarSetter() {
		$jar1                        = new Jar();
		$jar1['requests-testcookie'] = 'testvalue';

		$jar2 = new Jar(
			[
				'requests-testcookie' => 'testvalue',
			]
		);

		$this->assertEquals($jar1, $jar2, 'Jar objects are not equivalent of each other');
		$this->assertSame(
			$jar1['requests-testcookie'],
			$jar2['requests-testcookie'],
			'Array access did not yield the same value for the requested key on both objects'
		);
	}

	public function testJarUnsetter() {
		$jar                        = new Jar();
		$jar['requests-testcookie'] = 'testvalue';

		$this->assertSame('testvalue', $jar['requests-testcookie'], 'Initial value was not set to expected value');

		unset($jar['requests-testcookie']);

		$this->assertArrayNotHasKey('requests-testcookie', $jar, 'Jar array index "requests-testcookie" still exists after unsetting');
		$this->assertNull($jar['requests-testcookie'], 'Array access on unset key does not yield null');
	}

	public function testJarAsList() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Object is a dictionary, not a list');

		$cookies   = new Jar();
		$cookies[] = 'requests-testcookie1=testvalue1';
	}
}
