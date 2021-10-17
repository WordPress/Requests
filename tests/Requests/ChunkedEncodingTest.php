<?php

namespace WpOrg\Requests\Tests\Requests;

use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\Fixtures\TransportMock;
use WpOrg\Requests\Tests\TestCase;

final class ChunkedDecodingTest extends TestCase {

	/**
	 * @dataProvider dataChunked
	 */
	public function testChunked($body, $expected) {
		$transport          = new TransportMock();
		$transport->body    = $body;
		$transport->chunked = true;

		$options  = array(
			'transport' => $transport,
		);
		$response = Requests::get('http://example.com/', array(), $options);

		$this->assertSame($expected, $response->body);
	}

	public function dataChunked() {
		return array(
			array(
				'body'     => "25\r\nThis is the data in the first chunk\r\n\r\n1A\r\nand this is the second one\r\n0\r\n",
				'expected' => "This is the data in the first chunk\r\nand this is the second one",
			),
			array(
				'body'     => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0\r\nnothing\n",
				'expected' => "abra\ncadabra",
			),
			array(
				'body'     => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
				'expected' => "abra\ncadabra\nall we got\n",
			),
			array(
				'body'     => "02;foo=bar;hello=world\r\nab\r\n04;foo=baz\r\nra\nc\r\n06;justfoo\r\nadabra\r\n0c\r\n\nall we got\n",
				'expected' => "abra\ncadabra\nall we got\n",
			),
			array(
				'body'     => "02;foo=\"quoted value\"\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
				'expected' => "abra\ncadabra\nall we got\n",
			),
			array(
				'body'     => "02;foo-bar=baz\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
				'expected' => "abra\ncadabra\nall we got\n",
			),
		);
	}

	/**
	 * Response says it's chunked, but actually isn't
	 * @dataProvider dataNotActuallyChunked
	 */
	public function testNotActuallyChunked($body) {
		$transport          = new TransportMock();
		$transport->body    = $body;
		$transport->chunked = true;

		$options  = array(
			'transport' => $transport,
		);
		$response = Requests::get('http://example.com/', array(), $options);

		$this->assertSame($transport->body, $response->body);
	}

	public function dataNotActuallyChunked() {
		return array(
			'empty string'                         => array(''),
			'invalid chunk size'                   => array('Hello! This is a non-chunked response!'),
			'invalid chunk extension'              => array('1BNot chunked\r\nLooks chunked but it is not\r\n'),
			'unquoted chunk-ext-val with space'    => array("02;foo=unquoted with space\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n"),
			'unquoted chunk-ext-val with forbidden character' => array("02;foo={unquoted}\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n"),
			'invalid chunk-ext-name'               => array("02;{foo}=bar\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n"),
			'incomplete quote for chunk-ext-value' => array("02;foo=\"no end quote\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n"),
		);
	}

	/**
	 * Response says it's chunked and starts looking like it is, but turns out
	 * that they're lying to us
	 */
	public function testMixedChunkiness() {
		$transport          = new TransportMock();
		$transport->body    = "02\r\nab\r\nNot actually chunked!";
		$transport->chunked = true;

		$options  = array(
			'transport' => $transport,
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$this->assertSame($transport->body, $response->body);
	}
}
