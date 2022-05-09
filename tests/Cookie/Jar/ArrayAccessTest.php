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
		$this->assertEquals($jar1, $jar2);
	}

	public function testJarUnsetter() {
		$jar                        = new Jar();
		$jar['requests-testcookie'] = 'testvalue';

		$this->assertSame('testvalue', $jar['requests-testcookie']);

		unset($jar['requests-testcookie']);
		$this->assertEmpty($jar['requests-testcookie']);
		$this->assertFalse(isset($jar['requests-testcookie']));
	}

	public function testJarAsList() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Object is a dictionary, not a list');

		$cookies   = new Jar();
		$cookies[] = 'requests-testcookie1=testvalue1';
	}
}
