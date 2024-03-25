<?php

namespace WpOrg\Requests\Tests\Requests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\Fixtures\RawTransportMock;
use WpOrg\Requests\Tests\Fixtures\TransportFailedMock;
use WpOrg\Requests\Tests\Fixtures\TransportInvalidArgumentMock;
use WpOrg\Requests\Tests\Fixtures\TransportMock;
use WpOrg\Requests\Tests\Fixtures\TransportRedirectMock;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class RequestsTest extends TestCase {

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$url`.
	 *
	 * @dataProvider dataRequestInvalidUrl
	 *
	 * @covers \WpOrg\Requests\Requests::request
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testRequestInvalidUrl($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($url) must be of type string|Stringable');

		Requests::request($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataRequestInvalidUrl() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Tests receiving an exception when the request() method received an invalid input type as `$type`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Requests::request
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testRequestInvalidType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #4 ($type) must be of type string');

		Requests::request('/', [], [], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidTypeNotString() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Tests receiving an exception when the request() method received an invalid input type as `$options`.
	 *
	 * @dataProvider dataInvalidTypeNotArray
	 *
	 * @covers \WpOrg\Requests\Requests::request
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testRequestInvalidOptions($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #5 ($options) must be of type array');

		Requests::request('/', [], [], Requests::GET, $input);
	}

	/**
	 * Tests receiving an exception when the request_multiple() method received an invalid input type as `$requests`.
	 *
	 * @dataProvider dataRequestMultipleInvalidRequests
	 *
	 * @covers \WpOrg\Requests\Requests::request_multiple
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testRequestMultipleInvalidRequests($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($requests) must be of type array|ArrayAccess&Traversable');

		Requests::request_multiple($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataRequestMultipleInvalidRequests() {
		$except = array_intersect(TypeProviderHelper::GROUP_ITERABLE, TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
		return TypeProviderHelper::getAllExcept($except);
	}

	/**
	 * Tests receiving an exception when the request_multiple() method received an invalid input type as `$option`.
	 *
	 * @dataProvider dataInvalidTypeNotArray
	 *
	 * @covers \WpOrg\Requests\Requests::request_multiple
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testRequestMultipleInvalidOptions($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($options) must be of type array');

		Requests::request_multiple([], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidTypeNotArray() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	public function testInvalidProtocol() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Only HTTP(S) requests are handled');
		Requests::request('ftp://128.0.0.1/');
	}

	public function testDefaultTransport() {
		$request = Requests::get(new Iri($this->httpbin('/get')));
		$this->assertSame(200, $request->status_code);
	}

	public function testTransportFailedTriggersRequestsFailedCallback() {
		$mock = $this->getMockedStdClassWithMethods(['failed']);
		$mock->expects($this->once())->method('failed');
		$hooks = new Hooks();
		$hooks->register('requests.failed', [$mock, 'failed']);

		$transport = new TransportFailedMock();

		$options = [
			'hooks'     => $hooks,
			'transport' => $transport,
		];

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Transport failed!');
		Requests::get('http://example.com/', [], $options);
	}

	public function testTransportInvalidArgumentTriggersRequestsFailedCallback() {
		$mock = $this->getMockedStdClassWithMethods(['failed']);
		$mock->expects($this->once())->method('failed');
		$hooks = new Hooks();
		$hooks->register('requests.failed', [$mock, 'failed']);

		$transport = new TransportInvalidArgumentMock();

		$options = [
			'hooks'     => $hooks,
			'transport' => $transport,
		];

		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($url) must be of type string|Stringable');
		Requests::get('http://example.com/', [], $options);
	}

	/**
	 * Standard response header parsing
	 */
	public function testHeaderParsing() {
		$transport       = new RawTransportMock();
		$transport->data =
			"HTTP/1.0 200 OK\r\n" .
			"Host: localhost\r\n" .
			"Host: ambiguous\r\n" .
			"Nospace:here\r\n" .
			"Muchspace:  there   \r\n" .
			"Empty:\r\n" .
			"Empty2: \r\n" .
			"Folded: one\r\n" .
			"\ttwo\r\n" .
			"  three\r\n\r\n" .
			"stop\r\n";

		$options               = [
			'transport' => $transport,
		];
		$response              = Requests::get('http://example.com/', [], $options);
		$expected              = new Headers();
		$expected['host']      = 'localhost,ambiguous';
		$expected['nospace']   = 'here';
		$expected['muchspace'] = 'there';
		$expected['empty']     = '';
		$expected['empty2']    = '';
		$expected['folded']    = 'one two  three';
		foreach ($expected as $key => $value) {
			$this->assertSame($value, $response->headers[$key]);
		}

		foreach ($response->headers as $key => $value) {
			$this->assertSame($value, $expected[$key]);
		}
	}

	public function testProtocolVersionParsing() {
		$transport       = new RawTransportMock();
		$transport->data =
			"HTTP/1.0 200 OK\r\n" .
			"Host: localhost\r\n\r\n";

		$options = [
			'transport' => $transport,
		];

		$response = Requests::get('http://example.com/', [], $options);
		$this->assertSame(1.0, $response->protocol_version);
	}

	public function testRawAccess() {
		$transport       = new RawTransportMock();
		$transport->data =
			"HTTP/1.0 200 OK\r\n" .
			"Host: localhost\r\n\r\n" .
			'Test';

		$options  = [
			'transport' => $transport,
		];
		$response = Requests::get('http://example.com/', [], $options);
		$this->assertSame($transport->data, $response->raw);
	}

	/**
	 * Headers with only \n delimiting should be treated as if they're \r\n
	 */
	public function testHeaderOnlyLF() {
		$transport       = new RawTransportMock();
		$transport->data = "HTTP/1.0 200 OK\r\nTest: value\nAnother-Test: value\r\n\r\n";

		$options  = [
			'transport' => $transport,
		];
		$response = Requests::get('http://example.com/', [], $options);
		$this->assertSame('value', $response->headers['test']);
		$this->assertSame('value', $response->headers['another-test']);
	}

	/**
	 * Check that invalid protocols are not accepted
	 *
	 * We do not support HTTP/0.9. If this is really an issue for you, file a
	 * new issue, and update your server/proxy to support a proper protocol.
	 */
	public function testInvalidProtocolVersion() {
		$transport       = new RawTransportMock();
		$transport->data = "HTTP/0.9 200 OK\r\n\r\n<p>Test";

		$options = [
			'transport' => $transport,
		];

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Response could not be parsed');
		Requests::get('http://example.com/', [], $options);
	}

	/**
	 * Check that invalid protocols are not accepted
	 *
	 * We do not support HTTP/0.9. If this is really an issue for you, file a
	 * new issue, and update your server/proxy to support a proper protocol.
	 */
	public function testInvalidProtocolVersionTriggersRequestsFailedCallback() {
		$mock = $this->getMockedStdClassWithMethods(['failed']);
		$mock->expects($this->once())->method('failed');
		$hooks = new Hooks();
		$hooks->register('requests.failed', [$mock, 'failed']);

		$transport       = new RawTransportMock();
		$transport->data = "HTTP/0.9 200 OK\r\n\r\n<p>Test";

		$options = [
			'hooks'     => $hooks,
			'transport' => $transport,
		];

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Response could not be parsed');
		Requests::get('http://example.com/', [], $options);
	}

	/**
	 * HTTP/0.9 also appears to use a single CRLF instead of two.
	 */
	public function testSingleCRLFSeparator() {
		$transport       = new RawTransportMock();
		$transport->data = "HTTP/0.9 200 OK\r\n<p>Test";

		$options = [
			'transport' => $transport,
		];

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Missing header/body separator');
		Requests::get('http://example.com/', [], $options);
	}

	/**
	 * HTTP/0.9 also appears to use a single CRLF instead of two.
	 */
	public function testSingleCRLFSeparatorTriggersRequestsFailedCallback() {
		$mock = $this->getMockedStdClassWithMethods(['failed']);
		$mock->expects($this->once())->method('failed');
		$hooks = new Hooks();
		$hooks->register('requests.failed', [$mock, 'failed']);

		$transport       = new RawTransportMock();
		$transport->data = "HTTP/0.9 200 OK\r\n<p>Test";

		$options = [
			'hooks'     => $hooks,
			'transport' => $transport,
		];

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Missing header/body separator');
		Requests::get('http://example.com/', [], $options);
	}

	public function testInvalidStatus() {
		$transport       = new RawTransportMock();
		$transport->data = "HTTP/1.1 OK\r\nTest: value\nAnother-Test: value\r\n\r\nTest";

		$options = [
			'transport' => $transport,
		];

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Response could not be parsed');
		Requests::get('http://example.com/', [], $options);
	}

	public function testInvalidStatusTriggersRequestsFailedCallback() {
		$mock = $this->getMockedStdClassWithMethods(['failed']);
		$mock->expects($this->once())->method('failed');
		$hooks = new Hooks();
		$hooks->register('requests.failed', [$mock, 'failed']);

		$transport       = new RawTransportMock();
		$transport->data = "HTTP/1.1 OK\r\nTest: value\nAnother-Test: value\r\n\r\nTest";

		$options = [
			'hooks'     => $hooks,
			'transport' => $transport,
		];

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Response could not be parsed');
		Requests::get('http://example.com/', [], $options);
	}

	public function test30xWithoutLocation() {
		$transport       = new TransportMock();
		$transport->code = 302;

		$options  = [
			'transport' => $transport,
		];
		$response = Requests::get('http://example.com/', [], $options);
		$this->assertSame(302, $response->status_code);
		$this->assertSame(0, $response->redirects);
	}

	public function testRedirectToExceptionTriggersRequestsFailedCallbackOnce() {
		$mock = $this->getMockedStdClassWithMethods(['failed']);
		$mock->expects($this->once())->method('failed');
		$hooks = new Hooks();
		$hooks->register('requests.failed', [$mock, 'failed']);

		$transport                       = new TransportRedirectMock();
		$transport->redirected_transport = new TransportFailedMock();

		$options = [
			'hooks'     => $hooks,
			'transport' => $transport,
		];

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Transport failed!');

		$response = Requests::get('http://example.com/', [], $options);

		$this->assertSame(302, $response->status_code);
		$this->assertSame(1, $response->redirects);
	}

	public function testRedirectToInvalidArgumentTriggersRequestsFailedCallbackOnce() {
		$mock = $this->getMockedStdClassWithMethods(['failed']);
		$mock->expects($this->once())->method('failed');
		$hooks = new Hooks();
		$hooks->register('requests.failed', [$mock, 'failed']);

		$transport                       = new TransportRedirectMock();
		$transport->redirected_transport = new TransportInvalidArgumentMock();

		$options = [
			'hooks'     => $hooks,
			'transport' => $transport,
		];

		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($url) must be of type string|Stringable');

		$response = Requests::get('http://example.com/', [], $options);

		$this->assertSame(302, $response->status_code);
		$this->assertSame(1, $response->redirects);
	}

	public function testTimeoutException() {
		$options = ['timeout' => 0.5];
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('timed out');
		Requests::get($this->httpbin('/delay/3'), [], $options);
	}
}
