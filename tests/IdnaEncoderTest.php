<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\IdnaEncoder;
use WpOrg\Requests\Tests\TestCase;

final class IdnaEncoderTest extends TestCase {
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
		$result = IdnaEncoder::encode($data);
		$this->assertSame($expected, $result);
	}

	public function testASCIITooLong() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Provided string is too long');
		$data = str_repeat('abcd', 20);
		IdnaEncoder::encode($data);
	}

	public function testEncodedTooLong() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Encoded string is too long');
		$data = str_repeat("\xe4\xbb\x96", 60);
		IdnaEncoder::encode($data);
	}

	public function testAlreadyPrefixed() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Provided string begins with ACE prefix');
		IdnaEncoder::encode("xn--\xe4\xbb\x96");
	}

	public function testASCIICharacter() {
		$result = IdnaEncoder::encode('a');
		$this->assertSame('a', $result);
	}

	public function testTwoByteCharacter() {
		$result = IdnaEncoder::encode("\xc2\xb6"); // Pilcrow character
		$this->assertSame('xn--tba', $result);
	}

	public function testThreeByteCharacter() {
		$result = IdnaEncoder::encode("\xe2\x82\xac"); // Euro symbol
		$this->assertSame('xn--lzg', $result);
	}

	public function testFourByteCharacter() {
		$result = IdnaEncoder::encode("\xf0\xa4\xad\xa2"); // Chinese symbol?
		$this->assertSame('xn--ww6j', $result);
	}

	public function testFiveByteCharacter() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Unicode codepoint');
		IdnaEncoder::encode("\xfb\xb6\xb6\xb6\xb6");
	}

	public function testSixByteCharacter() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Unicode codepoint');
		IdnaEncoder::encode("\xfd\xb6\xb6\xb6\xb6\xb6");
	}

	public function testInvalidASCIICharacterWithMultibyte() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Unicode codepoint');
		IdnaEncoder::encode("\0\xc2\xb6");
	}

	public function testUnfinishedMultibyte() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Unicode codepoint');
		IdnaEncoder::encode("\xc2");
	}

	public function testPartialMultibyte() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Unicode codepoint');
		IdnaEncoder::encode("\xc2\xc2\xb6");
	}
}
