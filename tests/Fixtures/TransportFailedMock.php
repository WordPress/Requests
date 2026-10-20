<?php

namespace WpOrg\Requests\Tests\Fixtures;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Transport;

final class TransportFailedMock implements Transport {
	public function request($url, $headers = [], $data = [], $options = []) {
		throw new Exception('Transport failed!', 'transporterror');
	}
	public function request_multiple($requests, $options) {
		throw new Exception('Transport failed!', 'transporterror');
	}
	public static function test($capabilities = []) {
		return true;
	}
}
