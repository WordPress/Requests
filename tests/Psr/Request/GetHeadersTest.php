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
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertSame([], $request->getHeaders());
	}
}
