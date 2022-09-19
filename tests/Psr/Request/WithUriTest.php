<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class WithUriTest extends TestCase {

	/**
	 * Tests changing the uri when using withUri().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withUri
	 *
	 * @return void
	 */
	public function testWithUriReturnsRequest() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');

		$this->assertInstanceOf(RequestInterface::class, $request->withUri($uri));
	}

	/**
	 * Tests changing the uri when using withUri().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withUri
	 *
	 * @return void
	 */
	public function testWithUriReturnsNewInstance() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');

		$this->assertNotSame($request, $request->withUri($uri));
	}

	/**
	 * Tests changing the uri when using withUri().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withUri
	 *
	 * @return void
	 */
	public function testWithUriChangesTheUri() {
		$uri1 = $this->createMock(UriInterface::class);
		$request = Request::withMethodAndUri('GET', $uri1);

		$uri2 = $this->createMock(UriInterface::class);
		$uri2->method('getHost')->willReturn('');
		$request = $request->withUri($uri2);

		$this->assertSame($uri2, $request->getUri());
	}

	/**
	 * Tests changing the uri when using withUri().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withUri
	 *
	 * @return void
	 */
	public function testWithUriChangesTheHostHeader() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('example.org');
		$request = Request::withMethodAndUri('GET', $uri);

		$this->assertSame(['Host' => ['example.org']], $request->getHeaders());

		$uri2 = $this->createMock(UriInterface::class);
		$uri2->method('getHost')->willReturn('example.com');
		$request = $request->withUri($uri2);

		$this->assertSame(['Host' => ['example.com']], $request->getHeaders());
	}

	/**
	 * Tests changing the uri when using withUri().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withUri
	 *
	 * @return void
	 */
	public function testWithUriChangesTheHostHeaderToFirstPlace() {
		$uri = $this->createMock(UriInterface::class);
		$uri->method('getHost')->willReturn('');
		$request = Request::withMethodAndUri('GET', $uri);
		$request = $request->withHeader('name', 'value');

		$this->assertSame(['name' => ['value']], $request->getHeaders());

		$uri2 = $this->createMock(UriInterface::class);
		$uri2->method('getHost')->willReturn('example.com');
		$request = $request->withUri($uri2);

		$this->assertSame(['Host' => ['example.com'], 'name' => ['value']], $request->getHeaders());
	}
}
