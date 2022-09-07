<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class WithMethodAndUriTest extends TestCase {

	/**
	 * Tests receiving a Request instance when using withMethodAndUri().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withMethodAndUri
	 *
	 * @return void
	 */
	public function testWithMethodAndUriReturnsRequest() {
		$uri = $this->createMock(UriInterface::class);

		$this->assertInstanceOf(
			RequestInterface::class,
			Request::withMethodAndUri('', $uri)
		);
	}

	/**
	 * Tests receiving an exception when using withMethodAndUri().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::withMethodAndUri
	 *
	 * @return void
	 */
	public function testWithMethodAndUriWithoutMethodStringThrowsException() {
		$method = null;
		$uri = $this->createMock(UriInterface::class);

		$this->expectException(Exception::class);

		Request::withMethodAndUri($method, $uri);
	}
}
