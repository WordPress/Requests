<?php

namespace WpOrg\Requests\Tests\Requests;

use ArrayIterator;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Tests\Fixtures\RawTransportMock;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\Fixtures\TransportMock;
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
	public function dataRequestInvalidUrl() {
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
	public function dataInvalidTypeNotString() {
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
	public function dataRequestMultipleInvalidRequests() {
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
	public function dataInvalidTypeNotArray() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	public function testInvalidProtocol() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Only HTTP(S) requests are handled');
		Requests::request('ftp://128.0.0.1/');
	}

	public function testDefaultTransport() {
		$request = Requests::get(new Iri(httpbin('/get')));
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

	public function testTimeoutException() {
		$options = ['timeout' => 0.5];
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('timed out');
		Requests::get(httpbin('/delay/3'), [], $options);
	}

	/**
	 * Tests setting a custom certificate path with valid data types (though potentially not a valid path).
	 *
	 * @dataProvider dataSetCertificatePathValidData
	 *
	 * @covers \WpOrg\Requests\Requests::set_certificate_path
	 *
	 * @param mixed $input Valid input.
	 *
	 * @return void
	 */
	public function testSetCertificatePathValidData($input) {
		Requests::set_certificate_path($input);

		$this->assertSame($input, Requests::get_certificate_path());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataSetCertificatePathValidData() {
		return [
			'boolean false'     => [false],
			'boolean true'      => [true],
			'string'            => ['path/to/file.pem'],
			'stringable object' => [new StringableObject('path/to/file.pem')],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as `$path` to the set_certificate_path() method.
	 *
	 * @dataProvider dataSetCertificatePathInvalidData
	 *
	 * @covers \WpOrg\Requests\Requests::set_certificate_path
	 *
	 * @param mixed $input Invalid input.
	 *
	 * @return void
	 */
	public function testSetCertificatePathInvalidData($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($path) must be of type string|Stringable|bool');

		Requests::set_certificate_path($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataSetCertificatePathInvalidData() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_BOOL, TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Tests flattening of data arrays.
	 *
	 * @dataProvider dataFlattenValidData
	 *
	 * @covers \WpOrg\Requests\Requests::flatten
	 *
	 * @param mixed $input Valid input.
	 *
	 * @return void
	 */
	public function testFlattenValidData($input) {
		$expected = [
			0 => 'key1: value1',
			1 => 'key2: value2',
		];

		$this->assertSame($expected, Requests::flatten($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataFlattenValidData() {
		$to_flatten = ['key1' => 'value1', 'key2' => 'value2'];

		return [
			'array'           => [$to_flatten],
			'iterable object' => [new ArrayIterator($to_flatten)],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed as `$dictionary` to the flatten() method.
	 *
	 * @dataProvider dataFlattenInvalidData
	 *
	 * @covers \WpOrg\Requests\Requests::flatten
	 *
	 * @param mixed $input Invalid input.
	 *
	 * @return void
	 */
	public function testFlattenInvalidData($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($dictionary) must be of type iterable');

		Requests::flatten($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataFlattenInvalidData() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ITERABLE);
	}
}
