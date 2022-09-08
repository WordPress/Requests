<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;

final class GetUserInfoTest extends TestCase {

	/**
	 * Tests receiving the user-info when using getUserInfo().
	 *
	 * @dataProvider dataGetUserInfo
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::getUserInfo
	 *
	 * @return void
	 */
	public function testGetUserInfo($input, $expected) {
		$uri = Uri::fromIri(new Iri($input));

		$this->assertSame($expected, $uri->getUserInfo());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataGetUserInfo() {
		return [
			'empty' => ['', ''],
			'without user-info' => ['https://@example.org', ''],
			'with user-info and password' => ['https://user:pass@example.org', 'user:pass'],
			'with user-info' => ['https://user@example.org', 'user'],
			'with password' => ['https://:pass@example.org', ':pass'],
		];
	}
}
