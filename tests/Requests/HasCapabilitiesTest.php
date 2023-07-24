<?php

namespace WpOrg\Requests\Tests\Requests;

use ReflectionProperty;
use WpOrg\Requests\Capability;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\Fixtures\TestTransportMock;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Requests::has_capabilities
 */
final class HasCapabilitiesTest extends TestCase {

	public function testSucceedsForDetectingSsl() {
		if (!extension_loaded('curl') && !extension_loaded('openssl')) {
			$this->markTestSkipped('Testing for SSL requires either the curl or the openssl extension');
		}

		$this->assertTrue(Requests::has_capabilities([Capability::SSL => true]));
	}

	public function testFailsForUnsupportedCapabilities() {
		$transports = new ReflectionProperty(Requests::class, 'transports');
		$transports->setAccessible(true);
		$transports->setValue(null, [TestTransportMock::class]);

		$result = Requests::has_capabilities(['time-travel' => true]);

		$transports->setValue(null, []);
		$transports->setAccessible(false);

		$this->assertFalse($result);
	}
}
