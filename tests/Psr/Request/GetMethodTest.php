<?php

namespace WpOrg\Requests\Tests\Psr\Request;

use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Psr\Request;
use WpOrg\Requests\Tests\TestCase;

final class GetMethodTest extends TestCase {

	/**
	 * Tests receiving the method when using getMethod().
	 *
	 * @covers \WpOrg\Requests\Psr\Request::getMethod
	 *
	 * @return void
	 */
	public function testGetMethodReturnsString() {
		$request = Request::withMethodAndUri('GET', $this->createMock(UriInterface::class));

		$this->assertSame('GET', $request->getMethod());
	}
}
