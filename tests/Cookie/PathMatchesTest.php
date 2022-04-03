<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers WpOrg\Requests\Cookie::path_matches
 */
final class PathMatchesTest extends TestCase {

	/**
	 * @dataProvider dataPathMatch
	 */
	public function testPathMatch($original, $check, $matches) {
		$attributes         = new CaseInsensitiveDictionary();
		$attributes['path'] = $original;
		$cookie             = new Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertSame($matches, $cookie->path_matches($check));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataPathMatch() {
		return [
			'Invalid check path (type): null'    => ['/', null, true],
			'Invalid check path (type): true'    => ['/', true, false],
			'Invalid check path (type): integer' => ['/', 123, false],
			'Invalid check path (type): array'   => ['/', [1, 2], false],
			['/', '', true],
			['/', '/', true],

			['/', '/test', true],
			['/', '/test/', true],

			['/test', '/', false],
			['/test', '/test', true],
			['/test', '/testing', false],
			['/test', '/test/', true],
			['/test', '/test/ing', true],
			['/test', '/test/ing/', true],

			['/test/', '/test/', true],
			['/test/', '/', false],
		];
	}
}
