<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\IdnaEncoder;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\IdnaEncoder
 */
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
		return [
			'empty string' => [
				'data'     => '',
				'expected' => '',
			],
			'ascii character' => [
				'data'     => 'a',
				'expected' => 'a',
			],
			'two-byte character' => [
				'data'     => "\xc2\xb6", // Pilcrow character
				'expected' => 'xn--tba',
			],
			'three-byte character' => [
				'data'     => "\xe2\x82\xac", // Euro symbol
				'expected' => 'xn--lzg',
			],
			'four-byte character' => [
				'data'     => "\xf0\xa4\xad\xa2", // Chinese symbol?
				'expected' => 'xn--ww6j',
			],

			'stringable object' => [
				'data'     => new StringableObject("\xc2\xb6"),
				'expected' => 'xn--tba',
			],

			/*
			 * Examples taken from RFC: https://datatracker.ietf.org/doc/html/rfc3492#section-7
			 *
			 * Testdata retrieved by converting to hex using https://r12a.github.io/uniview/
			 * - Paste the unicode sequence.
			 * - Use the "Remove all spaces" option.
			 * - Use the "Send to Unicode Converter Tool" option.
			 * - In the tool, copy the UTF-8 sequence, lowercase it and add the `\x` between each set.
			 */
			'example from specs: RFC3492, section 7.1-A: Arabic' => [
				'data'     => "\xd9\x84\xd9\x8a\xd9\x87\xd9\x85\xd8\xa7\xd8\xa8\xd8\xaa\xd9\x83\xd9\x84\xd9\x85\xd9\x88\xd8\xb4\xd8\xb9\xd8\xb1\xd8\xa8\xd9\x8a\xd8\x9f",
				'expected' => 'xn--egbpdaj6bu4bxfgehfvwxn',
			],
			'example from specs: RFC3492, section 7.1-B: Simplified Chinese' => [
				'data'     => "\xe4\xbb\x96\xe4\xbb\xac\xe4\xb8\xba\xe4\xbb\x80\xe4\xb9\x88\xe4\xb8\x8d\xe8\xaf\xb4\xe4\xb8\xad\xe6\x96\x87",
				'expected' => 'xn--ihqwcrb4cv8a8dqg056pqjye',
			],
			'example from specs: RFC3492, section 7.1-C: Traditional Chinese' => [
				'data'     => "\xe4\xbb\x96\xe5\x80\x91\xe7\x88\xb2\xe4\xbb\x80\xe9\xba\xbd\xe4\xb8\x8d\xe8\xaa\xaa\xe4\xb8\xad\xe6\x96\x87",
				'expected' => 'xn--ihqwctvzc91f659drss3x8bo0yb',
			],
			'example from specs: RFC3492, section 7.1-D: Czech' => [
				'data'     => "\x50\x72\x6f\xc4\x8d\x70\x72\x6f\x73\x74\xc4\x9b\x6e\x65\x6d\x6c\x75\x76\xc3\xad\xc4\x8d\x65\x73\x6b\x79",
				'expected' => 'xn--Proprostnemluvesky-uyb24dma41a',
			],
			'example from specs: RFC3492, section 7.1-E: Hebrew' => [
				'data'     => "\xd7\x9c\xd7\x9e\xd7\x94\xd7\x94\xd7\x9d\xd7\xa4\xd7\xa9\xd7\x95\xd7\x98\xd7\x9c\xd7\x90\xd7\x9e\xd7\x93\xd7\x91\xd7\xa8\xd7\x99\xd7\x9d\xd7\xa2\xd7\x91\xd7\xa8\xd7\x99\xd7\xaa",
				'expected' => 'xn--4dbcagdahymbxekheh6e0a7fei0b',
			],
			'example from specs: RFC3492, section 7.1-F: Hindi (Devanagari)' => [
				'data'     => "\xe0\xa4\xaf\xe0\xa4\xb9\xe0\xa4\xb2\xe0\xa5\x8b\xe0\xa4\x97\xe0\xa4\xb9\xe0\xa4\xbf\xe0\xa4\xa8\xe0\xa5\x8d\xe0\xa4\xa6\xe0\xa5\x80\xe0\xa4\x95\xe0\xa5\x8d\xe0\xa4\xaf\xe0\xa5\x8b\xe0\xa4\x82\xe0\xa4\xa8\xe0\xa4\xb9\xe0\xa5\x80\xe0\xa4\x82\xe0\xa4\xac\xe0\xa5\x8b\xe0\xa4\xb2\xe0\xa4\xb8\xe0\xa4\x95\xe0\xa4\xa4\xe0\xa5\x87\xe0\xa4\xb9\xe0\xa5\x88\xe0\xa4\x82",
				'expected' => 'xn--i1baa7eci9glrd9b2ae1bj0hfcgg6iyaf8o0a1dig0cd',
			],
			'example from specs: RFC3492, section 7.1-G: Japanese (Kanji and hiragana)' => [
				'data'     => "\xe3\x81\xaa\xe3\x81\x9c\xe3\x81\xbf\xe3\x82\x93\xe3\x81\xaa\xe6\x97\xa5\xe6\x9c\xac\xe8\xaa\x9e\xe3\x82\x92\xe8\xa9\xb1\xe3\x81\x97\xe3\x81\xa6\xe3\x81\x8f\xe3\x82\x8c\xe3\x81\xaa\xe3\x81\x84\xe3\x81\xae\xe3\x81\x8b",
				'expected' => 'xn--n8jok5ay5dzabd5bym9f0cm5685rrjetr6pdxa',
			],
			/* Does not validate - output too long.
			'example from specs: RFC3492, section 7.1-H: Korean (Hangul)' => array(
				'data'     => "\xec\x84\xb8\xea\xb3\x84\xec\x9d\x98\xeb\xaa\xa8\xeb\x93\xa0\xec\x82\xac\xeb\x9e\x8c\xeb\x93\xa4\xec\x9d\xb4\xed\x95\x9c\xea\xb5\xad\xec\x96\xb4\xeb\xa5\xbc\xec\x9d\xb4\xed\x95\xb4\xed\x95\x9c\xeb\x8b\xa4\xeb\xa9\xb4\xec\x96\xbc\xeb\xa7\x88\xeb\x82\x98\xec\xa2\x8b\xec\x9d\x84\xea\xb9\x8c",
				'expected' => 'xn--989aomsvi5e83db1d2a355cv1e0vak1dwrv93d5xbh15a0dt30a5jpsd879ccm6fea98c',
			),
			*/
			'example from specs: RFC3492, section 7.1-I: Russian (Cyrillic)' => [
				'data'     => "\xd0\xbf\xd0\xbe\xd1\x87\xd0\xb5\xd0\xbc\xd1\x83\xd0\xb6\xd0\xb5\xd0\xbe\xd0\xbd\xd0\xb8\xd0\xbd\xd0\xb5\xd0\xb3\xd0\xbe\xd0\xb2\xd0\xbe\xd1\x80\xd1\x8f\xd1\x82\xd0\xbf\xd0\xbe\xd1\x80\xd1\x83\xd1\x81\xd1\x81\xd0\xba\xd0\xb8",
				// Officially, the `d` in `dot` should be uppercase ? Needs double-check. Either a typo in the RFC or a bug.
				'expected' => 'xn--b1abfaaepdrnnbgefbadotcwatmq2g4l',
			],
			'example from specs: RFC3492, section 7.1-J: Spanish' => [
				'data'     => "\x50\x6f\x72\x71\x75\xc3\xa9\x6e\x6f\x70\x75\x65\x64\x65\x6e\x73\x69\x6d\x70\x6c\x65\x6d\x65\x6e\x74\x65\x68\x61\x62\x6c\x61\x72\x65\x6e\x45\x73\x70\x61\xc3\xb1\x6f\x6c",
				'expected' => 'xn--PorqunopuedensimplementehablarenEspaol-fmd56a',
			],
			'example from specs: RFC3492, section 7.1-K: Vietnamese' => [
				'data'     => "\x54\xe1\xba\xa1\x69\x73\x61\x6f\x68\xe1\xbb\x8d\x6b\x68\xc3\xb4\x6e\x67\x74\x68\xe1\xbb\x83\x63\x68\xe1\xbb\x89\x6e\xc3\xb3\x69\x74\x69\xe1\xba\xbf\x6e\x67\x56\x69\xe1\xbb\x87\x74",
				'expected' => 'xn--TisaohkhngthchnitingVit-kjcr8268qyxafd2f1b9g',
			],
			'example from specs: RFC3492, section 7.1-L: Japanese artist' => [
				'data'     => "\x33\xe5\xb9\xb4\x42\xe7\xb5\x84\xe9\x87\x91\xe5\x85\xab\xe5\x85\x88\xe7\x94\x9f",
				'expected' => 'xn--3B-ww4c5e180e575a65lsy2b',
			],
			'example from specs: RFC3492, section 7.1-M: Japanese artist' => [
				'data'     => "\xe5\xae\x89\xe5\xae\xa4\xe5\xa5\x88\xe7\xbe\x8e\xe6\x81\xb5\x2d\x77\x69\x74\x68\x2d\x53\x55\x50\x45\x52\x2d\x4d\x4f\x4e\x4b\x45\x59\x53",
				'expected' => 'xn---with-SUPER-MONKEYS-pc58ag80a8qai00g7n9n',
			],
			'example from specs: RFC3492, section 7.1-N: Japanese artist' => [
				'data'     => "\x48\x65\x6c\x6c\x6f\x2d\x41\x6e\x6f\x74\x68\x65\x72\x2d\x57\x61\x79\x2d\xe3\x81\x9d\xe3\x82\x8c\xe3\x81\x9e\xe3\x82\x8c\xe3\x81\xae\xe5\xa0\xb4\xe6\x89\x80",
				'expected' => 'xn--Hello-Another-Way--fc4qua05auwb3674vfr0b',
			],
			'example from specs: RFC3492, section 7.1-O: Japanese artist' => [
				'data'     => "\xe3\x81\xb2\xe3\x81\xa8\xe3\x81\xa4\xe5\xb1\x8b\xe6\xa0\xb9\xe3\x81\xae\xe4\xb8\x8b\x32",
				'expected' => 'xn--2-u9tlzr9756bt3uc0v',
			],
			'example from specs: RFC3492, section 7.1-P: Japanese artist' => [
				'data'     => "\x4d\x61\x6a\x69\xe3\x81\xa7\x4b\x6f\x69\xe3\x81\x99\xe3\x82\x8b\x35\xe7\xa7\x92\xe5\x89\x8d",
				'expected' => 'xn--MajiKoi5-783gue6qz075azm5e',
			],
			'example from specs: RFC3492, section 7.1-Q: Japanese artist' => [
				'data'     => "\xe3\x83\x91\xe3\x83\x95\xe3\x82\xa3\xe3\x83\xbc\x64\x65\xe3\x83\xab\xe3\x83\xb3\xe3\x83\x90",
				'expected' => 'xn--de-jg4avhby1noc0d',
			],
			'example from specs: RFC3492, section 7.1-R: Japanese artist' => [
				'data'     => "\xe3\x81\x9d\xe3\x81\xae\xe3\x82\xb9\xe3\x83\x94\xe3\x83\xbc\xe3\x83\x89\xe3\x81\xa7",
				'expected' => 'xn--d9juau41awczczp',
			],
			'example from specs: RFC3492, section 7.1-S: ASCII string which breaks the rules' => [
				'data'     => "\x2d\x3e\x20\x24\x31\x2e\x30\x30\x20\x3c\x2d",
				'expected' => '-> $1.00 <-',
			],
		];
	}

	/**
	 * Tests receiving an exception when trying to encode a hostname containing invalid unicode.
	 *
	 * @dataProvider dataInvalidUnicode
	 *
	 * @param string $data Data to encode.
	 *
	 * @return void
	 */
	public function testInvalidUnicode($data) {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Unicode codepoint');

		IdnaEncoder::encode($data);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidUnicode() {
		return [
			'Five-byte character'                    => ["\xfb\xb6\xb6\xb6\xb6"],
			'Six-byte character'                     => ["\xfd\xb6\xb6\xb6\xb6\xb6"],
			'Invalid ASCII character with multibyte' => ["\0\xc2\xb6"],
			'Unfinished multibyte'                   => ["\xc2"],
			'Partial multibyte'                      => ["\xc2\xc2\xb6"],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $data Data to encode.
	 *
	 * @return void
	 */
	public function testInvalidInputType($data) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($hostname) must be of type string|Stringable');

		IdnaEncoder::encode($data);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInputType() {
		return [
			'null'          => [null],
			'boolean false' => [false],
			'integer'       => [12345],
			'array'         => [[1, 2, 3]],
		];
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
}
