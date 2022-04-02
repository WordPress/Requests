<?php

namespace WpOrg\Requests\Tests;

use Exception;
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

	/**
	 * Helper function to convert a single-level array containing text strings to a named data provider.
	 *
	 * @param string[] $input Input array.
	 *
	 * @return array[] Array which is usable as a test data provider with named data sets.
	 */
	public function textArrayToDataprovider($input) {
		$data = [];
		foreach ($input as $value) {
			if (!is_string($value)) {
				throw new Exception(
					sprintf(
						'All values in the input array should be text strings. Fix the input data. Received: %s',
						var_export($value, true)
					)
				);
			}

			if (isset($data[$value])) {
				throw new Exception(
					"Attempting to add a duplicate data set for value $value to the data provider. Fix the input data."
				);
			}

			$data[$value] = [$value];
		}

		return $data;
	}
}
