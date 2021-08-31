<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\IdnaEncoder;
use WpOrg\Requests\Tests\TestCase;

final class IdnaEncoderTest extends TestCase {

	/**
	 * Tests encoding a hostname using Punycode.
	 *
	 * @dataProvider dataEncoding
	 *
	 * @param string $data     Data to encode.
	 * @param string $expected Expected function output.
	 *
	 * @return void
	 */
	public function testEncoding($data, $expected) {
		$result = IdnaEncoder::encode($data);
		$this->assertSame($expected, $result);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataEncoding() {
		return array(
			'ascii character' => array(
				'data'     => 'a',
				'expected' => 'a',
			),
			'two-byte character' => array(
				'data'     => "\xc2\xb6", // Pilcrow character
				'expected' => 'xn--tba',
			),
			'three-byte character' => array(
				'data'     => "\xe2\x82\xac", // Euro symbol
				'expected' => 'xn--lzg',
			),
			'four-byte character' => array(
				'data'     => "\xf0\xa4\xad\xa2", // Chinese symbol?
				'expected' => 'xn--ww6j',
			),
			'example from specs: RFC3492, section 7.1-B: Simplified Chinese' => array(
				'data'     => "\xe4\xbb\x96\xe4\xbb\xac\xe4\xb8\xba\xe4\xbb\x80\xe4\xb9\x88\xe4\xb8\x8d\xe8\xaf\xb4\xe4\xb8\xad\xe6\x96\x87",
				'expected' => 'xn--ihqwcrb4cv8a8dqg056pqjye',
			),
			'example from specs: RFC3492, section 7.1-L: Japanese artist' => array(
				'data'     => "\x33\xe5\xb9\xb4\x42\xe7\xb5\x84\xe9\x87\x91\xe5\x85\xab\xe5\x85\x88\xe7\x94\x9f",
				'expected' => 'xn--3B-ww4c5e180e575a65lsy2b',
			),
		);
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
