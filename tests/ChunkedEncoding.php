<?php

class RequestsTest_ChunkedDecoding extends PHPUnit_Framework_TestCase {
	public static function chunkedProvider() {
		return array(
			array(
				"25\r\nThis is the data in the first chunk\r\n\r\n1A\r\nand this is the second one\r\n0\r\n",
				"This is the data in the first chunk\r\nand this is the second one"
			),
			array(
				"02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0\r\nnothing\n",
				"abra\ncadabra"
			),
			array(
				"02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
				"abra\ncadabra\nall we got\n"
			),
		);
	}

	/**
	 * @dataProvider chunkedProvider
	 */
	public function testChunked($body, $expected){
		$transport = new MockTransport();
		$transport->body = $body;
		$transport->chunked = true;

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);

		$this->assertEquals($expected, $response->body);
	}
}