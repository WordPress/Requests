<?php

class IDNAEncoderTest extends PHPUnit_Framework_TestCase {
	public static function specExamples() {
		return array(
			array(
				"\xe4\xbb\x96\xe4\xbb\xac\xe4\xb8\xba\xe4\xbb\x80\xe4\xb9\x88\xe4\xb8\x8d\xe8\xaf\xb4\xe4\xb8\xad\xe6\x96\x87",
				"xn--ihqwcrb4cv8a8dqg056pqjye"
			),
			array(
				"\x33\xe5\xb9\xb4\x42\xe7\xb5\x84\xe9\x87\x91\xe5\x85\xab\xe5\x85\x88\xe7\x94\x9f",
				"xn--3B-ww4c5e180e575a65lsy2b",
			)
		);
	}

	/**
	 * @dataProvider specExamples
	 */
	public function testEncoding($data, $expected) {
		$result = Requests_IDNAEncoder::encode($data);
		$this->assertEquals($expected, $result);
	}
}