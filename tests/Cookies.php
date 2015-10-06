<?php

class RequestsTest_Cookies extends PHPUnit_Framework_TestCase {
	public function testBasicCookie() {
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue');

		$this->assertEquals('requests-testcookie', $cookie->name);
		$this->assertEquals('testvalue', $cookie->value);
		$this->assertEquals('testvalue', (string) $cookie);

		$this->assertEquals('requests-testcookie=testvalue', $cookie->formatForHeader());
		$this->assertEquals('requests-testcookie=testvalue', $cookie->formatForSetCookie());
	}

	public function testCookieWithAttributes() {
		$attributes = array(
			'httponly',
			'path' => '/'
		);
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue', $attributes);

		$this->assertEquals('requests-testcookie=testvalue', $cookie->formatForHeader());
		$this->assertEquals('requests-testcookie=testvalue; httponly; path=/', $cookie->formatForSetCookie());
	}

	public function testEmptyCookieName() {
		$cookie = Requests_Cookie::parse('test');
		$this->assertEquals('', $cookie->name);
		$this->assertEquals('test', $cookie->value);
	}

	public function testEmptyAttributes() {
		$cookie = Requests_Cookie::parse('foo=bar; HttpOnly');
		$this->assertTrue($cookie->attributes['httponly']);
	}

	public function testCookieJarSetter() {
		$jar1 = new Requests_Cookie_Jar();
		$jar1['requests-testcookie'] = 'testvalue';

		$jar2 = new Requests_Cookie_Jar(array(
			'requests-testcookie' => 'testvalue',
		));
		$this->assertEquals($jar1, $jar2);
	}

	public function testCookieJarUnsetter() {
		$jar = new Requests_Cookie_Jar();
		$jar['requests-testcookie'] = 'testvalue';

		$this->assertEquals('testvalue', $jar['requests-testcookie']);

		unset($jar['requests-testcookie']);
		$this->assertEmpty($jar['requests-testcookie']);
		$this->assertFalse(isset($jar['requests-testcookie']));
	}

	/**
	 * @expectedException Requests_Exception
	 */
	public function testCookieJarAsList() {
		$cookies = new Requests_Cookie_Jar();
		$cookies[] = 'requests-testcookie1=testvalue1';
	}

	public function testCookieJarIterator() {
		$cookies = array(
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		);
		$jar = new Requests_Cookie_Jar($cookies);

		foreach ($jar as $key => $value) {
			$this->assertEquals($cookies[$key], $value);
		}
	}

	public function testReceivingCookies() {
		$options = array(
			'follow_redirects' => false,
		);
		$url = httpbin('/cookies/set?requests-testcookie=testvalue');

		$response = Requests::get($url, array(), $options);

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertNotEmpty( $cookie );
		$this->assertEquals( 'testvalue', $cookie->value );
	}

	public function testPersistenceOnRedirect() {
		$options = array(
			'follow_redirects' => true,
		);
		$url = httpbin('/cookies/set?requests-testcookie=testvalue');

		$response = Requests::get($url, array(), $options);

		$cookie = $response->cookies['requests-testcookie'];
		$this->assertNotEmpty( $cookie );
		$this->assertEquals( 'testvalue', $cookie->value );
	}

	protected function setCookieRequest($cookies) {
		$options = array(
			'cookies' => $cookies,
		);
		$response = Requests::get(httpbin('/cookies/set'), array(), $options);

		$data = json_decode($response->body, true);
		$this->assertInternalType('array', $data);
		$this->assertArrayHasKey('cookies', $data);
		return $data['cookies'];
	}

	public function testSendingCookie() {
		$cookies = array(
			'requests-testcookie1' => 'testvalue1',
		);

		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertEquals('testvalue1', $data['requests-testcookie1']);
	}

	public function testSendingCookieWithJar() {
		$cookies = new Requests_Cookie_Jar(array(
			'requests-testcookie1' => 'testvalue1',
		));
		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertEquals('testvalue1', $data['requests-testcookie1']);
	}

	public function testSendingMultipleCookies() {
		$cookies = array(
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		);
		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertEquals('testvalue1', $data['requests-testcookie1']);

		$this->assertArrayHasKey('requests-testcookie2', $data);
		$this->assertEquals('testvalue2', $data['requests-testcookie2']);
	}

	public function testSendingMultipleCookiesWithJar() {
		$cookies = new Requests_Cookie_Jar(array(
			'requests-testcookie1' => 'testvalue1',
			'requests-testcookie2' => 'testvalue2',
		));
		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie1', $data);
		$this->assertEquals('testvalue1', $data['requests-testcookie1']);

		$this->assertArrayHasKey('requests-testcookie2', $data);
		$this->assertEquals('testvalue2', $data['requests-testcookie2']);
	}

	public function testSendingPrebakedCookie() {
		$cookies = new Requests_Cookie_Jar(array(
			new Requests_Cookie('requests-testcookie', 'testvalue'),
		));
		$data = $this->setCookieRequest($cookies);

		$this->assertArrayHasKey('requests-testcookie', $data);
		$this->assertEquals('testvalue', $data['requests-testcookie']);
	}

	public function domainMatchProvider() {
		return array(
			array('example.com', 'example.com',     true,  true),
			array('example.com', 'www.example.com', false, true),
			array('example.com', 'example.net',     false, false),

			// Leading period
			array('.example.com', 'example.com',     true,  true),
			array('.example.com', 'www.example.com', false, true),
			array('.example.com', 'example.net',     false, false),

			// Prefix, but not subdomain
			array('example.com', 'notexample.com',  false, false),
			array('example.com', 'notexample.net',  false, false),

			// Reject IP address prefixes
			array('127.0.0.1',   '127.0.0.1',     true, true),
			array('127.0.0.1',   'abc.127.0.0.1', false, false),
			array('127.0.0.1',   'example.com',   false, false),

			// Check that we're checking the actual length
			array('127.com', 'test.127.com', false, true),
		);
	}

	/**
	 * @dataProvider domainMatchProvider
	 */
	public function testDomainExactMatch($original, $check, $matches, $domain_matches) {
		$attributes = new Requests_Utility_CaseInsensitiveDictionary();
		$attributes['domain'] = $original;
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertEquals($matches, $cookie->domainMatches($check));
	}

	/**
	 * @dataProvider domainMatchProvider
	 */
	public function testDomainMatch($original, $check, $matches, $domain_matches) {
		$attributes = new Requests_Utility_CaseInsensitiveDictionary();
		$attributes['domain'] = $original;
		$flags = array(
			'host-only' => false
		);
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue', $attributes, $flags);
		$this->assertEquals($domain_matches, $cookie->domainMatches($check));
	}

	public function pathMatchProvider() {
		return array(
			array('/',      '/',      true),

			array('/',      '/test',  true),
			array('/',      '/test/', true),

			array('/test',  '/',          false),
			array('/test',  '/test',      true),
			array('/test',  '/testing',   false),
			array('/test',  '/test/',     true),
			array('/test',  '/test/ing',  true),
			array('/test',  '/test/ing/', true),

			array('/test/', '/test/', true),
			array('/test/', '/',      false),
		);
	}

	/**
	 * @dataProvider pathMatchProvider
	 */
	public function testPathMatch($original, $check, $matches) {
		$attributes = new Requests_Utility_CaseInsensitiveDictionary();
		$attributes['path'] = $original;
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertEquals($matches, $cookie->pathMatches($check));
	}

	public function urlMatchProvider() {
		return array(
			// Domain handling
			array( 'example.com', '/', 'http://example.com/',     true,  true ),
			array( 'example.com', '/', 'http://www.example.com/', false, true ),
			array( 'example.com', '/', 'http://example.net/',     false, false ),
			array( 'example.com', '/', 'http://www.example.net/', false, false ),

			// /test
			array( 'example.com', '/test', 'http://example.com/',            false, false ),
			array( 'example.com', '/test', 'http://www.example.com/',        false, false ),

			array( 'example.com', '/test', 'http://example.com/test',        true,  true ),
			array( 'example.com', '/test', 'http://www.example.com/test',    false, true ),

			array( 'example.com', '/test', 'http://example.com/testing',     false, false ),
			array( 'example.com', '/test', 'http://www.example.com/testing', false, false ),

			array( 'example.com', '/test', 'http://example.com/test/',       true,  true ),
			array( 'example.com', '/test', 'http://www.example.com/test/',   false, true ),

			// /test/
			array( 'example.com', '/test/', 'http://example.com/',     false, false ),
			array( 'example.com', '/test/', 'http://www.example.com/', false, false ),
		);
	}

	/**
	 * @depends testDomainExactMatch
	 * @depends testPathMatch
	 * @dataProvider urlMatchProvider
	 */
	public function testUrlExactMatch($domain, $path, $check, $matches, $domain_matches) {
		$attributes = new Requests_Utility_CaseInsensitiveDictionary();
		$attributes['domain'] = $domain;
		$attributes['path']   = $path;
		$check = new Requests_IRI($check);
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertEquals($matches, $cookie->uriMatches($check));
	}

	/**
	 * @depends testDomainMatch
	 * @depends testPathMatch
	 * @dataProvider urlMatchProvider
	 */
	public function testUrlMatch($domain, $path, $check, $matches, $domain_matches) {
		$attributes = new Requests_Utility_CaseInsensitiveDictionary();
		$attributes['domain'] = $domain;
		$attributes['path']   = $path;
		$flags = array(
			'host-only' => false
		);
		$check = new Requests_IRI($check);
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue', $attributes, $flags);
		$this->assertEquals($domain_matches, $cookie->uriMatches($check));
	}

	public function testUrlMatchSecure() {
		$attributes = new Requests_Utility_CaseInsensitiveDictionary();
		$attributes['domain'] = 'example.com';
		$attributes['path']   = '/';
		$attributes['secure'] = true;
		$flags = array(
			'host-only' => false,
		);
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue', $attributes, $flags);

		$this->assertTrue($cookie->uriMatches(new Requests_IRI('https://example.com/')));
		$this->assertFalse($cookie->uriMatches(new Requests_IRI('http://example.com/')));

		// Double-check host-only
		$this->assertTrue($cookie->uriMatches(new Requests_IRI('https://www.example.com/')));
		$this->assertFalse($cookie->uriMatches(new Requests_IRI('http://www.example.com/')));
	}

	/**
	 * Manually set cookies without a domain/path set should always be valid
	 *
	 * Cookies parsed from headers internally in Requests will always have a
	 * domain/path set, but those created manually will not. Manual cookies
	 * should be regarded as "global" cookies (that is, set for `.`)
	 */
	public function testUrlMatchManuallySet() {
		$cookie = new Requests_Cookie('requests-testcookie', 'testvalue');
		$this->assertTrue($cookie->domainMatches('example.com'));
		$this->assertTrue($cookie->domainMatches('example.net'));
		$this->assertTrue($cookie->pathMatches('/'));
		$this->assertTrue($cookie->pathMatches('/test'));
		$this->assertTrue($cookie->pathMatches('/test/'));
		$this->assertTrue($cookie->uriMatches(new Requests_IRI('http://example.com/')));
		$this->assertTrue($cookie->uriMatches(new Requests_IRI('http://example.com/test')));
		$this->assertTrue($cookie->uriMatches(new Requests_IRI('http://example.com/test/')));
		$this->assertTrue($cookie->uriMatches(new Requests_IRI('http://example.net/')));
		$this->assertTrue($cookie->uriMatches(new Requests_IRI('http://example.net/test')));
		$this->assertTrue($cookie->uriMatches(new Requests_IRI('http://example.net/test/')));
	}
}