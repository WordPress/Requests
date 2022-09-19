<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class GetBodyTest extends TestCase {

	/**
	 * Tests receiving the body when using getBody().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getBody
	 *
	 * @return void
	 */
	public function testGetBodyReturnsString() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertInstanceOf(StreamInterface::class, $request->getBody());
	}
}
