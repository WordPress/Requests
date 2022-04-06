<?php

namespace WpOrg\Requests\Tests\Cookie\Jar;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Cookie\Jar
 */
final class JarTest extends TestCase {

	public function testCookieJarSetter() {
		$jar1                        = new Jar();
		$jar1['requests-testcookie'] = 'testvalue';

		$jar2 = new Jar(
			[
				'requests-testcookie' => 'testvalue',
			]
		);
		$this->assertEquals($jar1, $jar2);
	}

	public function testCookieJarUnsetter() {
		$jar                        = new Jar();
		$jar['requests-testcookie'] = 'testvalue';

		$this->assertSame('testvalue', $jar['requests-testcookie']);

		unset($jar['requests-testcookie']);
		$this->assertEmpty($jar['requests-testcookie']);
		$this->assertFalse(isset($jar['requests-testcookie']));
	}

	public function testCookieJarAsList() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Object is a dictionary, not a list');
		$cookies   = new Jar();
		$cookies[] = 'requests-testcookie1=testvalue1';
	}

	public function testCookieJarIterator() {
		$cookies = [
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		];
		$jar     = new Jar($cookies);

		foreach ($jar as $key => $value) {
			$this->assertSame($cookies[$key], $value);
		}
	}

	public function testSendingCookieWithJar() {
		$cookies = new Jar(
			[
				'requests-testcookie1' => 'testvalue1',
			]
		);
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertSame('testvalue1', $data['requests-testcookie1']);
	}

	public function testSendingMultipleCookiesWithJar() {
		$cookies = new Jar(
			[
				'requests-testcookie1' => 'testvalue1',
				'requests-testcookie2' => 'testvalue2',
			]
		);
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertSame('testvalue1', $data['requests-testcookie1']);

		$this->assertArrayHasKey('requests-testcookie2', $data);
		$this->assertSame('testvalue2', $data['requests-testcookie2']);
	}

	public function testSendingPrebakedCookie() {
		$cookies = new Jar(
			[
				new Cookie('requests-testcookie', 'testvalue'),
			]
		);
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie', $data);
		$this->assertSame('testvalue', $data['requests-testcookie']);
	}

	private function setCookieRequest($cookies) {
		$options  = [
			'cookies' => $cookies,
		];
		$response = Requests::get(httpbin('/cookies/set'), [], $options);

		$data = json_decode($response->body, true);
		$this->assertIsArray($data);
		$this->assertArrayHasKey('cookies', $data);
		return $data['cookies'];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to the class constructor.
	 *
	 * @return void
	 */
	public function testConstructorInvalidInputType() {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($cookies) must be of type array');

		new Jar('string');
	}
}
