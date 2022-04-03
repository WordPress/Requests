<?php

namespace WpOrg\Requests\Tests\Session;

use WpOrg\Requests\Session;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers WpOrg\Requests\Session::__get
 * @covers WpOrg\Requests\Session::__set
 * @covers WpOrg\Requests\Session::__isset
 * @covers WpOrg\Requests\Session::__unset
 */
final class MagicPropertyAccessTest extends TestCase {

	public function testPropertyUsage() {
		$headers = [
			'X-TestHeader'  => 'testing',
			'X-TestHeader2' => 'requests-test',
		];
		$data    = [
			'testdata' => 'value1',
			'test2'    => 'value2',
			'test3'    => [
				'foo' => 'bar',
				'abc' => 'xyz',
			],
		];
		$options = [
			'testoption' => 'test',
			'foo'        => 'bar',
		];

		$session = new Session('http://example.com/', $headers, $data, $options);
		$this->assertSame('http://example.com/', $session->url);
		$this->assertSame($headers, $session->headers);
		$this->assertSame($data, $session->data);
		$this->assertSame($options['testoption'], $session->options['testoption']);

		// Test via property access
		$this->assertSame($options['testoption'], $session->testoption);

		// Test setting new property
		$session->newoption   = 'foobar';
		$options['newoption'] = 'foobar';
		$this->assertSame($options['newoption'], $session->options['newoption']);

		// Test unsetting property
		unset($session->newoption);
		$this->assertFalse(isset($session->newoption));

		// Update property
		$session->testoption   = 'foobar';
		$options['testoption'] = 'foobar';
		$this->assertSame($options['testoption'], $session->testoption);

		// Test getting invalid property
		$this->assertNull($session->invalidoption);
	}
}
