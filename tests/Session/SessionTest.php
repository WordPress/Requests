<?php

namespace WpOrg\Requests\Tests\Session;

use WpOrg\Requests\Session;
use WpOrg\Requests\Tests\TestCase;

final class SessionTest extends TestCase {
	public function testURLResolution() {
		$session = new Session(httpbin('/'));

		// Set the cookies up
		$response = $session->get('/get');
		$this->assertTrue($response->success, 'Session property "success" is not equal to true');
		$this->assertSame(
			httpbin('/get'),
			$response->url,
			'Session property "url" is not equal to the expected get URL'
		);

		$data = json_decode($response->body, true);
		$this->assertNotNull($data, 'Decoded response body is null');
		$this->assertArrayHasKey('url', $data, 'Response data array does not have key "url"');
		$this->assertSame(
			httpbin('/get'),
			$data['url'],
			'The value of the "url" key in the response data array is not equal to the expected get URL'
		);
	}

	public function testBasicGET() {
		$session_headers = [
			'X-Requests-Session' => 'BasicGET',
			'X-Requests-Request' => 'notset',
		];
		$session         = new Session(httpbin('/'), $session_headers);
		$response        = $session->get('/get', ['X-Requests-Request' => 'GET']);
		$response->throw_for_status(false);
		$this->assertSame(200, $response->status_code);

		$data = json_decode($response->body, true);
		$this->assertArrayHasKey('X-Requests-Session', $data['headers']);
		$this->assertSame('BasicGET', $data['headers']['X-Requests-Session']);
		$this->assertArrayHasKey('X-Requests-Request', $data['headers']);
		$this->assertSame('GET', $data['headers']['X-Requests-Request']);
	}

	public function testBasicHEAD() {
		$session_headers = [
			'X-Requests-Session' => 'BasicHEAD',
			'X-Requests-Request' => 'notset',
		];
		$session         = new Session(httpbin('/'), $session_headers);
		$response        = $session->head('/get', ['X-Requests-Request' => 'HEAD']);
		$response->throw_for_status(false);
		$this->assertSame(200, $response->status_code);
	}

	public function testBasicDELETE() {
		$session_headers = [
			'X-Requests-Session' => 'BasicDELETE',
			'X-Requests-Request' => 'notset',
		];
		$session         = new Session(httpbin('/'), $session_headers);
		$response        = $session->delete('/delete', ['X-Requests-Request' => 'DELETE']);
		$response->throw_for_status(false);
		$this->assertSame(200, $response->status_code);

		$data = json_decode($response->body, true);
		$this->assertArrayHasKey('X-Requests-Session', $data['headers']);
		$this->assertSame('BasicDELETE', $data['headers']['X-Requests-Session']);
		$this->assertArrayHasKey('X-Requests-Request', $data['headers']);
		$this->assertSame('DELETE', $data['headers']['X-Requests-Request']);
	}

	public function testBasicPOST() {
		$session_headers = [
			'X-Requests-Session' => 'BasicPOST',
			'X-Requests-Request' => 'notset',
		];
		$session         = new Session(httpbin('/'), $session_headers);
		$response        = $session->post('/post', ['X-Requests-Request' => 'POST'], ['postdata' => 'exists']);
		$response->throw_for_status(false);
		$this->assertSame(200, $response->status_code);

		$data = json_decode($response->body, true);
		$this->assertArrayHasKey('X-Requests-Session', $data['headers']);
		$this->assertSame('BasicPOST', $data['headers']['X-Requests-Session']);
		$this->assertArrayHasKey('X-Requests-Request', $data['headers']);
		$this->assertSame('POST', $data['headers']['X-Requests-Request']);
	}

	public function testBasicPUT() {
		$session_headers = [
			'X-Requests-Session' => 'BasicPUT',
			'X-Requests-Request' => 'notset',
		];
		$session         = new Session(httpbin('/'), $session_headers);
		$response        = $session->put('/put', ['X-Requests-Request' => 'PUT'], ['postdata' => 'exists']);
		$response->throw_for_status(false);
		$this->assertSame(200, $response->status_code);

		$data = json_decode($response->body, true);
		$this->assertArrayHasKey('X-Requests-Session', $data['headers']);
		$this->assertSame('BasicPUT', $data['headers']['X-Requests-Session']);
		$this->assertArrayHasKey('X-Requests-Request', $data['headers']);
		$this->assertSame('PUT', $data['headers']['X-Requests-Request']);
	}

	public function testBasicPATCH() {
		$session_headers = [
			'X-Requests-Session' => 'BasicPATCH',
			'X-Requests-Request' => 'notset',
		];
		$session         = new Session(httpbin('/'), $session_headers);
		$response        = $session->patch('/patch', ['X-Requests-Request' => 'PATCH'], ['postdata' => 'exists']);
		$response->throw_for_status(false);
		$this->assertSame(200, $response->status_code);

		$data = json_decode($response->body, true);
		$this->assertArrayHasKey('X-Requests-Session', $data['headers']);
		$this->assertSame('BasicPATCH', $data['headers']['X-Requests-Session']);
		$this->assertArrayHasKey('X-Requests-Request', $data['headers']);
		$this->assertSame('PATCH', $data['headers']['X-Requests-Request']);
	}

	public function testSharedCookies() {
		$session = new Session(httpbin('/'));

		$options  = [
			'follow_redirects' => false,
		];
		$response = $session->get('/cookies/set?requests-testcookie=testvalue', [], $options);
		$this->assertSame(302, $response->status_code);

		// Check the cookies
		$response = $session->get('/cookies');
		$this->assertTrue($response->success);

		// Check the response
		$data = json_decode($response->body, true);
		$this->assertNotNull($data);
		$this->assertArrayHasKey('cookies', $data);

		$cookies = [
			'requests-testcookie' => 'testvalue',
		];
		$this->assertSame($cookies, $data['cookies']);
	}
}
