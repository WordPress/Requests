<?php

namespace WpOrg\Requests\Tests\Requests;

use WpOrg\Requests\Requests;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\Fixtures\TransportMock;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Requests::decode_chunked
 */
final class DecodeChunkedTest extends TestCase {

	/**
	 * Test decoding chunked responses.
	 *
	 * @dataProvider dataChunked
	 *
	 * @param string $body     Not really chunked response body.
	 * @param string $expected Expected chunked data.
	 *
	 * @return void
	 */
	public function testChunked($body, $expected) {
		$transport          = new TransportMock();
		$transport->body    = $body;
		$transport->chunked = true;

		$options  = [
			'transport' => $transport,
		];
		$response = Requests::get('http://example.com/', [], $options);

		$this->assertInstanceOf(Response::class, $response, 'Response is not an instance of the Response class.');
		$this->assertSame($expected, $response->body, 'Response body does not match expected dechunked body');
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataChunked() {
		return [
			[
				'body'     => "25\r\nThis is the data in the first chunk\r\n\r\n1A\r\nand this is the second one\r\n0\r\n",
				'expected' => "This is the data in the first chunk\r\nand this is the second one",
			],
			[
				'body'     => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0\r\nnothing\n",
				'expected' => "abra\ncadabra",
			],
			[
				'body'     => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
				'expected' => "abra\ncadabra\nall we got\n",
			],
			[
				'body'     => "02;foo=bar;hello=world\r\nab\r\n04;foo=baz\r\nra\nc\r\n06;justfoo\r\nadabra\r\n0c\r\n\nall we got\n",
				'expected' => "abra\ncadabra\nall we got\n",
			],
			[
				'body'     => "02;foo=\"quoted value\"\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
				'expected' => "abra\ncadabra\nall we got\n",
			],
			[
				'body'     => "02;foo-bar=baz\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
				'expected' => "abra\ncadabra\nall we got\n",
			],
		];
	}

	/**
	 * Response says it's chunked, but actually isn't.
	 *
	 * @dataProvider dataNotActuallyChunked
	 *
	 * @param string $body Not really chunked response body.
	 *
	 * @return void
	 */
	public function testNotActuallyChunked($body) {
		$transport          = new TransportMock();
		$transport->body    = $body;
		$transport->chunked = true;

		$options  = [
			'transport' => $transport,
		];
		$response = Requests::get('http://example.com/', [], $options);

		$this->assertInstanceOf(Response::class, $response, 'Response is not an instance of the Response class.');
		$this->assertSame($transport->body, $response->body, 'Response body does not match original body');
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataNotActuallyChunked() {
		return [
			'empty string'                         => [''],
			'invalid chunk size'                   => ['Hello! This is a non-chunked response!'],
			'invalid chunk extension'              => ['1BNot chunked\r\nLooks chunked but it is not\r\n'],
			'unquoted chunk-ext-val with space'    => ["02;foo=unquoted with space\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n"],
			'unquoted chunk-ext-val with forbidden character' => ["02;foo={unquoted}\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n"],
			'invalid chunk-ext-name'               => ["02;{foo}=bar\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n"],
			'incomplete quote for chunk-ext-value' => ["02;foo=\"no end quote\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n"],
		];
	}

	/**
	 * Response says it's chunked and starts looking like it is, but turns out
	 * that they're lying to us.
	 *
	 * @return void
	 */
	public function testMixedChunkiness() {
		$transport          = new TransportMock();
		$transport->body    = "02\r\nab\r\nNot actually chunked!";
		$transport->chunked = true;

		$options  = [
			'transport' => $transport,
		];
		$response = Requests::get('http://example.com/', [], $options);

		$this->assertInstanceOf(Response::class, $response, 'Response is not an instance of the Response class.');
		$this->assertSame($transport->body, $response->body, 'Response body does not match original body');
	}
}
