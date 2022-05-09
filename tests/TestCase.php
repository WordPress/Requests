<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TypeProviderHelper;
use Yoast\PHPUnitPolyfills\TestCases\TestCase as Polyfill_TestCase;

abstract class TestCase extends Polyfill_TestCase {

	/**
	 * Clean up after the tests.
	 *
	 * As most test classes will use the TypeProviderHelper for one or more tests,
	 * we may as well always call the clean up function just to be on the safe side.
	 */
	public static function tear_down_after_test() {
		TypeProviderHelper::cleanUp();
	}

	/**
	 * Helper function to skip select tests when the transport under test is not available.
	 *
	 * @param string $transport Fully qualified class name for the transport to verify.
	 *
	 * @return void
	 */
	public function skipWhenTransportNotAvailable($transport) {
		if (!$transport::test()) {
			$this->markTestSkipped('Transport "' . $transport . '" is not available');
		}
	}

	/**
	 * Data provider for use in tests which need to be run against all default supported transports.
	 *
	 * @return array
	 */
	public function transportProvider() {
		$data = [];

		foreach (Requests::DEFAULT_TRANSPORTS as $transport) {
			$name        = substr($transport, (strrpos($transport, '\\') + 1));
			$data[$name] = [$transport];
		}

		return $data;
	}
}
