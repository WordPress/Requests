<?php

namespace WpOrg\Requests\Tests\Fixtures;

use WpOrg\Requests\Transport;

final class TestTransportMock implements Transport {
	public function request($url, $headers = array(), $data = array(), $options = array()) {
		return '';
	}
	public function request_multiple($requests, $options) {
		return array();
	}
	public static function test($capabilities = array()) {
		// Time travel is not yet supported by this transport.
		if (isset($capabilities['time-travel']) && $capabilities['time-travel']) {
			return false;
		}
		return true;
	}
}
