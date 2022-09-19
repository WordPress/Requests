<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class GetProtocolVersionTest extends TestCase {

	/**
	 * Tests receiving the protocol version when using getProtocolVersion().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getProtocolVersion
	 *
	 * @return void
	 */
	public function testGetProtocolVersionReturnsString() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertSame('1.1', $request->getProtocolVersion());
	}
}
