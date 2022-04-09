<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TestCase;

final class CookieTest extends TestCase {
	public function testReceivingCookies() {
		$options = [
			'follow_redirects' => false,
		];
		$url     = httpbin('/cookies/set?requests-testcookie=testvalue');

		$response = Requests::get($url, [], $options);

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertNotEmpty($cookie);
		$this->assertSame('testvalue', $cookie->value);
	}

	public function testPersistenceOnRedirect() {
		$options = [
			'follow_redirects' => true,
		];
		$url     = httpbin('/cookies/set?requests-testcookie=testvalue');

		$response = Requests::get($url, [], $options);

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertNotEmpty($cookie);
		$this->assertSame('testvalue', $cookie->value);
	}

	public function testSendingCookie() {
		$cookies = [
			'requests-testcookie1' => 'testvalue1',
		];

		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertSame('testvalue1', $data['requests-testcookie1']);
	}

	/**
	 * @depends testSendingCookie
	 */
	public function testCookieExpiration() {
		$options = [
			'follow_redirects' => true,
		];
		$url     = httpbin('/cookies/set/testcookie/testvalue');
		$url    .= '?expiry=1';

		$response = Requests::get($url, [], $options);
		$response->throw_for_status();

		$data = json_decode($response->body, true);
		$this->assertEmpty($data['cookies']);
	}

	public function testSendingMultipleCookies() {
		$cookies = [
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		];
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertSame('testvalue1', $data['requests-testcookie1']);

		$this->assertArrayHasKey('requests-testcookie2', $data);
		$this->assertSame('testvalue2', $data['requests-testcookie2']);
	}

	/**
	 * Test helper.
	 *
	 * @param array $cookies The cookies to set.
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
