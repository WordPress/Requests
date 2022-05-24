<?php

namespace WpOrg\Requests\Tests\Cookie\Jar;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TestCase;

/**
 * Integration tests for the Jar class.
 *
 * @covers \WpOrg\Requests\Cookie\Jar::register
 * @covers \WpOrg\Requests\Cookie\Jar::before_request
 * @covers \WpOrg\Requests\Cookie\Jar::before_redirect_check
 */
final class JarTest extends TestCase {

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

	/**
	 * Test helper.
	 *
	 * @param \WpOrg\Requests\Cookie\Jar $cookies Cookies.
	 *
	 * @return array
	 */
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
}
