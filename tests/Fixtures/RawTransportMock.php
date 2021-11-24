<?php

namespace WpOrg\Requests\Tests\Fixtures;

use WpOrg\Requests\Transport;

final class RawTransportMock implements Transport {
	public $data = '';
	public function request($url, $headers = [], $data = [], $options = []) {
		return $this->data;
	}
	public function request_multiple($requests, $options) {
		foreach ($requests as $id => &$request) {
			$handler       = new self();
			$handler->data = $request['options']['raw.data'];
			$request       = $handler->request($request['url'], $request['headers'], $request['data'], $request['options']);
		}

		return $requests;
	}
	public static function test($capabilities = []) {
		return true;
	}
}
