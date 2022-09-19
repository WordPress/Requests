<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class GetHeadersTest extends TestCase {

	/**
	 * Tests receiving the headers when using getHeaders().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getHeaders
	 *
	 * @return void
	 */
	public function testGetHeadersReturnsArray() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');
		$request = Request::withMethodAndUri('GET', $uri);

		$this->assertSame([], $request->getHeaders());
	}

	/**
	 * Tests receiving the headers when using getHeaders().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getHeaders
	 *
	 * @return void
	 */
	public function testGetHeadersReturnsArrayWithHostHeader() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('example.org');
		$request = Request::withMethodAndUri('GET', $uri);

		$this->assertSame(['Host' => ['example.org']], $request->getHeaders());
	}
}
