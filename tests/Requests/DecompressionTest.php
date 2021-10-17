<?php

namespace WpOrg\Requests\Tests\Requests;

use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TestCase;

final class DecompressionTest extends TestCase {

	/**
	 * @dataProvider dataGzip
	 * @dataProvider dataDeflate
	 * @dataProvider dataDeflateWithoutHeaders
	 */
	public function testDecompress($expected, $compressed) {
		$decompressed = Requests::decompress($compressed);
		$this->assertSame($expected, $decompressed);
	}

	/**
	 * @dataProvider dataGzip
	 * @dataProvider dataDeflate
	 * @dataProvider dataDeflateWithoutHeaders
	 */
	public function testCompatibleInflate($expected, $compressed) {
		$decompressed = Requests::compatible_gzinflate($compressed);
		$this->assertSame($expected, $decompressed);
	}

	public function dataGzip() {
		return array(
			'gzip: foobar' => array(
				'expected'   => 'foobar',
				'compressed' => "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\x03\x4b\xcb\xcf\x4f\x4a"
							. "\x2c\x02\x00\x95\x1f\xf6\x9e\x06\x00\x00\x00",
			),
			'gzip: Requests for PHP' => array(
				'expected'   => 'Requests for PHP',
				'compressed' => "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\x03\x0b\x4a\x2d\x2c\x4d"
							. "\x2d\x2e\x29\x56\x48\xcb\x2f\x52\x08\xf0\x08\x00\x00\x58\x35"
							. "\x18\x17\x10\x00\x00\x00",
			),
		);
	}

	public function dataDeflate() {
		return array(
			'deflate: foobar' => array(
				'expected'   => 'foobar',
				'compressed' => "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\x03\x78\x9c\x4b\xcb\xcf"
					. "\x4f\x4a\x2c\x02\x00\x08\xab\x02\x7a",
			),
			'deflate: Requests for PHP' => array(
				'expected'   => 'Requests for PHP',
				'compressed' => "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\x03\x78\x9c\x0b\x4a\x2d"
							. "\x2c\x4d\x2d\x2e\x29\x56\x48\xcb\x2f\x52\x08\xf0\x08\x00\x00"
							. "\x34\x68\x05\xcc",
			),
		);
	}

	public function dataDeflateWithoutHeaders() {
		return array(
			'deflate without zlib headers: foobar' => array(
				'expected'   => 'foobar',
				'compressed' => "\x78\x9c\x4b\xcb\xcf\x4f\x4a\x2c\x02\x00\x08\xab\x02\x7a",
			),
			'deflate without zlib headers: Requests for PHP' => array(
				'expected'   => 'Requests for PHP',
				'compressed' => "\x78\x9c\x0b\x4a\x2d\x2c\x4d\x2d\x2e\x29\x56\x48\xcb\x2f\x52"
							. "\x08\xf0\x08\x00\x00\x34\x68\x05\xcc",
			),
			'deflate without zlib headers: compression level 1' => array(
				'expected'   => 'compression level 1',
				'compressed' => "\x78\x01\x4b\xce\xcf\x2d\x28\x4a\x2d\x2e\xce\xcc\xcf\x53\xc8\x49"
							. "\x2d\x4b\xcd\x51\x30\x04\x00\x4d\x86\x07\x3c",
			),
			'deflate without zlib headers: compression level 3' => array(
				'expected'   => 'compression level 3',
				'compressed' => "\x78\x5e\x4b\xce\xcf\x2d\x28\x4a\x2d\x2e\xce\xcc\xcf\x53\xc8\x49"
							. "\x2d\x4b\xcd\x51\x30\x06\x00\x4d\x88\x07\x3e",
			),
			'deflate without zlib headers: compression level 9' => array(
				'expected'   => 'compression level 9',
				'compressed' => "\x78\xda\x4b\xce\xcf\x2d\x28\x4a\x2d\x2e\xce\xcc\xcf\x53\xc8\x49"
							. "\x2d\x4b\xcd\x51\xb0\x04\x00\x4d\x8e\x07\x44",
			),
		);
	}
}
