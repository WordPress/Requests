<?php

namespace WpOrg\Requests\Tests\Cookie\Jar;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;

/**
 * Integration tests for the Jar class.
 *
 * @covers \WpOrg\Requests\Cookie\Jar::register
 * @covers \WpOrg\Requests\Cookie\Jar::before_request
 * @covers \WpOrg\Requests\Cookie\Jar::before_redirect_check
 */
final class JarTest extends TestCase {

	public function testSendingCookieWithEmptyJar() {
		$cookies = new Jar();
		$data    = $this->setCookieRequest($cookies);

		$this->assertCount(0, $data);
	}

	public function testSendingCookieWithJar() {
		$cookies = new Jar(
			[
				'requests-testcookie1' => 'testvalue1',
			]
		);
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data, 'Key "requests-testcookie1" does not exist in the array');
		$this->assertSame('testvalue1', $data['requests-testcookie1'], 'Value for cookie does not match expectation');
	}

	public function testSendingMultipleCookiesWithJar() {
		$cookies = new Jar(
			[
				'requests-testcookie1' => 'testvalue1',
				'requests-testcookie2' => 'testvalue2',
			]
		);
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data, 'Key "requests-testcookie1" does not exist in the array');
		$this->assertSame('testvalue1', $data['requests-testcookie1'], 'Value for cookie 1 does not match expectation');

		$this->assertArrayHasKey('requests-testcookie2', $data, 'Key "requests-testcookie2" does not exist in the array');
		$this->assertSame('testvalue2', $data['requests-testcookie2'], 'Value for cookie 2 does not match expectation');
	}

	public function testSendingPrebakedCookie() {
		$cookies = new Jar(
			[
				new Cookie('requests-testcookie', 'testvalue'),
			]
		);
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie', $data, 'Key "requests-testcookie" does not exist in the array');
		$this->assertSame('testvalue', $data['requests-testcookie'], 'Value for cookie does not match expectation');
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

		$this->assertInstanceOf(Response::class, $response, 'GET request did not return a Response object');
		$data = json_decode($response->body, true);

		$this->assertIsArray($data, 'Decoded response is not an array');
		$this->assertArrayHasKey('cookies', $data, 'Decoded response array does not contain the key "cookie"');
		$this->assertIsArray($data['cookies'], '"Cookie" key in the decoded response is not an array');

		return $data['cookies'];
	}
}
