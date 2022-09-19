<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class GetUriTest extends TestCase {

	/**
	 * Tests receiving the uri when using getUri().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getUri
	 *
	 * @return void
	 */
	public function testGetUriReturnsUriInterface() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertInstanceOf(UriInterface::class, $request->getUri());
	}
}
