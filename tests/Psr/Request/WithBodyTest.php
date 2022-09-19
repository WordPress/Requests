<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class WithBodyTest extends TestCase {

	/**
	 * Tests changing the body when using withBody().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withBody
	 *
	 * @return void
	 */
	public function testWithBodyReturnsRequest() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$body = $this->createMock(StreamInterface::class);

		$this->assertInstanceOf(RequestInterface::class, $request->withBody($body));
	}

	/**
	 * Tests changing the body when using withBody().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withBody
	 *
	 * @return void
	 */
	public function testWithBodyReturnsNewInstance() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$body = $this->createMock(StreamInterface::class);

		$this->assertNotSame($request, $request->withBody($body));
	}
}
