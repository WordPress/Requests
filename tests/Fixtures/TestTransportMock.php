<?php

namespace WpOrg\Requests\Tests\Fixtures;

use WpOrg\Requests\Transport;

final class TestTransportMock implements Transport {
	public function request($url, $headers = [], $data = [], $options = []) {
		return '';
	}
	public function request_multiple($requests, $options) {
		return [];
	}
	public static function test($capabilities = []) {
		// Time travel is not yet supported by this transport.
		if (isset($capabilities['time-travel']) && $capabilities['time-travel']) {
			return false;
		}

		return true;
	}
}
