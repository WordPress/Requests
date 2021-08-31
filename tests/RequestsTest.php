<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\Fixtures\RawTransportMock;
use WpOrg\Requests\Tests\Fixtures\TransportMock;
use WpOrg\Requests\Tests\TestCase;

final class RequestsTest extends TestCase {

	public function testInvalidProtocol() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Only HTTP(S) requests are handled');
		Requests::request('ftp://128.0.0.1/');
	}

	public function testDefaultTransport() {
		$request = Requests::get(httpbin('/get'));
		$this->assertSame(200, $request->status_code);
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

		$options               = array(
			'transport' => $transport,
		);
		$response              = Requests::get('http://example.com/', array(), $options);
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

		$options = array(
			'transport' => $transport,
		);

		$response = Requests::get('http://example.com/', array(), $options);
		$this->assertSame(1.0, $response->protocol_version);
	}

	public function testRawAccess() {
		$transport       = new RawTransportMock();
		$transport->data =
			"HTTP/1.0 200 OK\r\n" .
			"Host: localhost\r\n\r\n" .
			'Test';

		$options  = array(
			'transport' => $transport,
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$this->assertSame($transport->data, $response->raw);
	}

	/**
	 * Headers with only \n delimiting should be treated as if they're \r\n
	 */
	public function testHeaderOnlyLF() {
		$transport       = new RawTransportMock();
		$transport->data = "HTTP/1.0 200 OK\r\nTest: value\nAnother-Test: value\r\n\r\n";

		$options  = array(
			'transport' => $transport,
		);
		$response = Requests::get('http://example.com/', array(), $options);
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

		$options = array(
			'transport' => $transport,
		);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Response could not be parsed');
		Requests::get('http://example.com/', array(), $options);
	}

	/**
	 * HTTP/0.9 also appears to use a single CRLF instead of two.
	 */
	public function testSingleCRLFSeparator() {
		$transport       = new RawTransportMock();
		$transport->data = "HTTP/0.9 200 OK\r\n<p>Test";

		$options = array(
			'transport' => $transport,
		);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Missing header/body separator');
		Requests::get('http://example.com/', array(), $options);
	}

	public function testInvalidStatus() {
		$transport       = new RawTransportMock();
		$transport->data = "HTTP/1.1 OK\r\nTest: value\nAnother-Test: value\r\n\r\nTest";

		$options = array(
			'transport' => $transport,
		);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Response could not be parsed');
		Requests::get('http://example.com/', array(), $options);
	}

	public function test30xWithoutLocation() {
		$transport       = new TransportMock();
		$transport->code = 302;

		$options  = array(
			'transport' => $transport,
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$this->assertSame(302, $response->status_code);
		$this->assertSame(0, $response->redirects);
	}

	public function testTimeoutException() {
		$options = array('timeout' => 0.5);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('timed out');
		Requests::get(httpbin('/delay/3'), array(), $options);
	}
}
