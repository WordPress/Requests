<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers WpOrg\Requests\Cookie::path_matches
 */
final class PathMatchesTest extends TestCase {

	/**
	 * Manually set cookies without a domain/path set should always be valid.
	 *
	 * Cookies parsed from headers internally in Requests will always have a
	 * domain/path set, but those created manually will not. Manual cookies
	 * should be regarded as "global" cookies (that is, set for `.`).
	 *
	 * @dataProvider dataManuallySetCookie
	 *
	 * @param string $path Path to verify for a match.
	 *
	 * @return void
	 */
	public function testManuallySetCookie($path) {
		$cookie = new Cookie('requests-testcookie', 'testvalue');

		$this->assertTrue($cookie->path_matches($path));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataManuallySetCookie() {
		$paths = [
			'',
			'/',
			'/test',
			'/test/',
		];

		return $this->textArrayToDataprovider($paths);
	}

	/**
	 * @dataProvider dataPathMatchUndesiredInputTypes
	 * @dataProvider dataPathMatch
	 */
	public function testPathMatch($original, $check, $matches) {
		$attributes         = new CaseInsensitiveDictionary();
		$attributes['path'] = $original;
		$cookie             = new Cookie('requests-testcookie', 'testvalue', $attributes);
		$this->assertSame($matches, $cookie->path_matches($check));
	}

	/**
	 * Data provider for checking data type handling.
	 *
	 * @return array
	 */
	public function dataPathMatchUndesiredInputTypes() {
		$data      = [];
		$all_types = TypeProviderHelper::getAll();
		foreach ($all_types as $key => $value) {
			if (in_array($key, TypeProviderHelper::GROUP_EMPTY, true)) {
				$data['Match:     "/" vs ' . $key] = [
					'original' => '/',
					'check'    => $value['input'],
					'matches'  => true,
				];

				$data['Non-match: "/test" vs ' . $key] = [
					'original' => '/test',
					'check'    => $value['input'],
					'matches'  => false,
				];

				continue;
			}

			/*
			 * The other type inputs should all lead to a `false` result.
			 * Non-scalar types for being non-scalar and the string types for not actually being a path.
			 */
			$data['Non-match: "/" vs ' . $key] = [
				'original' => '/',
				'check'    => $value['input'],
				'matches'  => false,
			];
		}

		return $data;
	}

	/**
	 * Data provider for checking the actual functionality.
	 *
	 * @return array
	 */
	public function dataPathMatch() {
		return [
			'Exact match: "/"' => [
				'original' => '/',
				'check'    => '/',
				'matches'  => true,
			],
			'Exact match: "/test"' => [
				'original' => '/test',
				'check'    => '/test',
				'matches'  => true,
			],
			'Exact match: "/test/" (with trailing slash' => [
				'original' => '/test/',
				'check'    => '/test/',
				'matches'  => true,
			],

			'Partial match: "/" vs "/test"' => [
				'original' => '/',
				'check'    => '/test',
				'matches'  => true,
			],
			'Partial match: "/" vs "/test/" (with trailing slash)' => [
				'original' => '/',
				'check'    => '/test/',
				'matches'  => true,
			],
			'Partial match: "/test/" (with trailing slash) vs "/test/ing"' => [
				'original' => '/test/',
				'check'    => '/test/ing',
				'matches'  => true,
			],
			'Partial match: "/test" vs "/test/" (without vs with trailing slash)' => [
				'original' => '/test',
				'check'    => '/test/',
				'matches'  => true,
			],
			'Partial match: "/test" vs "/test/ing"' => [
				'original' => '/test',
				'check'    => '/test/ing',
				'matches'  => true,
			],
			'Partial match: "/test" vs "/test/ing/" (with trailing slash)' => [
				'original' => '/test',
				'check'    => '/test/ing/',
				'matches'  => true,
			],

			'Partial non-match: "/test" vs "/"' => [
				'original' => '/test',
				'check'    => '/',
				'matches'  => false,
			],
			'Partial non-match: "/test" vs "/testing"' => [
				'original' => '/test',
				'check'    => '/testing',
				'matches'  => false,
			],
			'Partial non-match: "/test/" (with trailing slash) vs "/"' => [
				'original' => '/test/',
				'check'    => '/',
				'matches'  => false,
			],
		];
	}
}
