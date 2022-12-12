<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Cookie\Jar;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;

/**
 * Integration tests for the Cookie class.
 *
 * @covers \WpOrg\Requests\Cookie
 * @covers \WpOrg\Requests\Cookie\Jar
 */
final class CookieTest extends TestCase {

	public function testReceivingCookies() {
		$options = [
			'follow_redirects' => false,
		];
		$url     = $this->httpbin('/cookies/set?requests-testcookie=testvalue');

		$response = Requests::get($url, [], $options);
		$this->assertInstanceOf(Response::class, $response, 'Requests::get() did not return a Response object');

		$this->assertInstanceof(Jar::class, $response->cookies, 'Cookies aren\'t in a Jar object');
		$this->assertArrayHasKey('requests-testcookie', $response->cookies, 'Key "requests-testcookie" does not exist in the array');

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertInstanceof(Cookie::class, $cookie, 'requests-testcookie cookie is not a Cookie object');
		$this->assertSame('testvalue', $cookie->value, 'Value for cookie does not match expectation');
	}

	public function testPersistenceOnRedirect() {
		$options = [
			'follow_redirects' => true,
		];
		$url     = $this->httpbin('/cookies/set?requests-testcookie=testvalue');

		$response = Requests::get($url, [], $options);
		$this->assertInstanceOf(Response::class, $response, 'Requests::get() did not return a Response object');

		$this->assertInstanceof(Jar::class, $response->cookies, 'Cookies aren\'t in a Jar object');
		$this->assertArrayHasKey('requests-testcookie', $response->cookies, 'Key "requests-testcookie" does not exist in the array');

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertInstanceof(Cookie::class, $cookie, 'requests-testcookie cookie is not a Cookie object');
		$this->assertSame('testvalue', $cookie->value, 'Value for cookie does not match expectation');
	}

	public function testSendingCookie() {
		$cookies = [
			'requests-testcookie1' => 'testvalue1',
		];

		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data, 'Key "requests-testcookie1" does not exist in the array');
		$this->assertSame('testvalue1', $data['requests-testcookie1'], 'Value for cookie does not match expectation');
	}

	/**
	 * @depends testSendingCookie
	 */
	public function testCookieExpiration() {
		$options = [
			'follow_redirects' => true,
		];
		$url     = $this->httpbin('/cookies/set/testcookie/testvalue');
		$url    .= '?expiry=1';

		$response = Requests::get($url, [], $options);
		$this->assertInstanceOf(Response::class, $response, 'Requests::get() did not return a Response object');

		$response->throw_for_status();

		$data = json_decode($response->body, true);

		$this->assertIsArray($data, 'Decoded response body is not an array');
		$this->assertArrayHasKey('cookies', $data, 'Response data array does not have key "cookies"');
		$this->assertIsArray($data['cookies'], 'Response data "cookies" value is not an array');
		$this->assertEmpty($data['cookies'], 'Response data "cookies" array is not empty');
	}

	public function testSendingMultipleCookies() {
		$cookies = [
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		];
		$data    = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data, 'Key "requests-testcookie1" does not exist in the array');
		$this->assertSame('testvalue1', $data['requests-testcookie1'], 'Value for cookie 1 does not match expectation');

		$this->assertArrayHasKey('requests-testcookie2', $data, 'Key "requests-testcookie2" does not exist in the array');
		$this->assertSame('testvalue2', $data['requests-testcookie2'], 'Value for cookie 2 does not match expectation');
	}

	/**
	 * Test helper.
	 *
	 * @param array $cookies The cookies to set.
	 *
	 * @return array
	 */
	private function setCookieRequest($cookies) {
		$options = [
			'cookies' => $cookies,
		];

		$response = Requests::get($this->httpbin('/cookies/set'), [], $options);
		$this->assertInstanceOf(Response::class, $response, 'Requests::get() did not return a Response object');

		$data = json_decode($response->body, true);

		$this->assertIsArray($data, 'Decoded response body is not an array');
		$this->assertArrayHasKey('cookies', $data, 'Response data array does not have key "cookies"');
		$this->assertIsArray($data['cookies'], 'Response data "cookies" value is not an array');

		return $data['cookies'];
	}
}
