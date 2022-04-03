<?php

namespace WpOrg\Requests\Tests\Session;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Response;
use WpOrg\Requests\Session;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

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

	public function testMultiple() {
		$session   = new Session(httpbin('/'), ['X-Requests-Session' => 'Multiple']);
		$requests  = [
			'test1' => [
				'url' => httpbin('/get'),
			],
			'test2' => [
				'url' => httpbin('/get'),
			],
		];
		$responses = $session->request_multiple($requests);

		// test1
		$this->assertNotEmpty($responses['test1']);
		$this->assertInstanceOf(Response::class, $responses['test1']);
		$this->assertSame(200, $responses['test1']->status_code);

		$result = json_decode($responses['test1']->body, true);
		$this->assertSame(httpbin('/get'), $result['url']);
		$this->assertEmpty($result['args']);

		// test2
		$this->assertNotEmpty($responses['test2']);
		$this->assertInstanceOf(Response::class, $responses['test2']);
		$this->assertSame(200, $responses['test2']->status_code);

		$result = json_decode($responses['test2']->body, true);
		$this->assertSame(httpbin('/get'), $result['url']);
		$this->assertEmpty($result['args']);
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

	/**
	 * Tests receiving an exception when the request_multiple() method received an invalid input type as `$requests`.
	 *
	 * @dataProvider dataRequestMultipleInvalidRequests
	 *
	 * @covers \WpOrg\Requests\Session::request_multiple
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testRequestMultipleInvalidRequests($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($requests) must be of type array|ArrayAccess&Traversable');

		$session = new Session();
		$session->request_multiple($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataRequestMultipleInvalidRequests() {
		$except = array_intersect(TypeProviderHelper::GROUP_ITERABLE, TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
		return TypeProviderHelper::getAllExcept($except);
	}

	/**
	 * Tests receiving an exception when the request_multiple() method received an invalid input type as `$option`.
	 *
	 * @dataProvider dataInvalidTypeNotArray
	 *
	 * @covers \WpOrg\Requests\Session::request_multiple
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testRequestMultipleInvalidOptions($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($options) must be of type array');

		$session = new Session();
		$session->request_multiple([], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotArray() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}
}
