<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class GetRequestTargetTest extends TestCase {

	/**
	 * Tests receiving the request target when using getRequestTarget().
	 *
	 * @dataProvider dataGetRequestTarget
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getRequestTarget
	 *
	 * @param string $path
	 * @param string $query
	 *
	 * @return void
	 */
	public function testGetRequestTargetReturnsString($path, $query, $expected) {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getPath')->willReturn($path);
		$uri->method('getQuery')->willReturn($query);

		$request = Request::withMethodAndUri('GET', $uri);

		$this->assertSame($expected, $request->getRequestTarget());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataGetRequestTarget() {
		return [
			'Retrieves the message request target' => ['path', '', 'path'],
			'Retrieves the message request target with query' => ['path', 'foo=bar', 'path?foo=bar'],
			'If no URI is available, this method MUST return the string "/"' => ['', '', '/'],
			'If no URI is available, this method MUST return the string "/" with query' => ['', 'foo=bar', '/?foo=bar'],
		];
	}
}
