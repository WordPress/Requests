<?php

class RequestsTest_IDNAEncoder extends PHPUnit_Framework_TestCase {
	public static function specExamples() {
		return array(
			array(
				"\xe4\xbb\x96\xe4\xbb\xac\xe4\xb8\xba\xe4\xbb\x80\xe4\xb9\x88\xe4\xb8\x8d\xe8\xaf\xb4\xe4\xb8\xad\xe6\x96\x87",
				'xn--ihqwcrb4cv8a8dqg056pqjye',
			),
			array(
				"\x33\xe5\xb9\xb4\x42\xe7\xb5\x84\xe9\x87\x91\xe5\x85\xab\xe5\x85\x88\xe7\x94\x9f",
				'xn--3B-ww4c5e180e575a65lsy2b',
			),
		);
	}

	/**
	 * @dataProvider specExamples
	 */
	public function testEncoding($data, $expected) {
		$result = Requests_IDNAEncoder::encode($data);
		$this->assertSame($expected, $result);
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Provided string is too long
	 */
	public function testASCIITooLong() {
		$data = str_repeat('abcd', 20);
		Requests_IDNAEncoder::encode($data);
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Encoded string is too long
	 */
	public function testEncodedTooLong() {
		$data = str_repeat("\xe4\xbb\x96", 60);
		Requests_IDNAEncoder::encode($data);
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Provided string begins with ACE prefix
	 */
	public function testAlreadyPrefixed() {
		Requests_IDNAEncoder::encode("xn--\xe4\xbb\x96");
	}

	public function testASCIICharacter() {
		$result = Requests_IDNAEncoder::encode('a');
		$this->assertSame('a', $result);
	}

	public function testTwoByteCharacter() {
		$result = Requests_IDNAEncoder::encode("\xc2\xb6"); // Pilcrow character
		$this->assertSame('xn--tba', $result);
	}

	public function testThreeByteCharacter() {
		$result = Requests_IDNAEncoder::encode("\xe2\x82\xac"); // Euro symbol
		$this->assertSame('xn--lzg', $result);
	}

	public function testFourByteCharacter() {
		$result = Requests_IDNAEncoder::encode("\xf0\xa4\xad\xa2"); // Chinese symbol?
		$this->assertSame('xn--ww6j', $result);
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Invalid Unicode codepoint
	 */
	public function testFiveByteCharacter() {
		Requests_IDNAEncoder::encode("\xfb\xb6\xb6\xb6\xb6");
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Invalid Unicode codepoint
	 */
	public function testSixByteCharacter() {
		Requests_IDNAEncoder::encode("\xfd\xb6\xb6\xb6\xb6\xb6");
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Invalid Unicode codepoint
	 */
	public function testInvalidASCIICharacterWithMultibyte() {
		Requests_IDNAEncoder::encode("\0\xc2\xb6");
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Invalid Unicode codepoint
	 */
	public function testUnfinishedMultibyte() {
		Requests_IDNAEncoder::encode("\xc2");
	}

	/**
	 * @expectedException        Requests_Exception
	 * @expectedExceptionMessage Invalid Unicode codepoint
	 */
	public function testPartialMultibyte() {
		Requests_IDNAEncoder::encode("\xc2\xc2\xb6");
	}
}
