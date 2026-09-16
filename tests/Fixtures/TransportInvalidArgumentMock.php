<?php

namespace WpOrg\Requests\Tests\Fixtures;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Transport;

final class TransportInvalidArgumentMock implements Transport {
	public function request($url, $headers = [], $data = [], $options = []) {
		throw InvalidArgument::create(1, '$url', 'string|Stringable', gettype($url));
	}
	public function request_multiple($requests, $options) {
		throw InvalidArgument::create(1, '$requests', 'array|ArrayAccess&Traversable', gettype($requests));
	}
	public static function test($capabilities = []) {
		return true;
	}
}
