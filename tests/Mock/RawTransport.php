<?php

class RequestsTest_Mock_RawTransport implements Requests_Transport {
	public $data = '';
	public function request($url, $headers = array(), $data = array(), $options = array()) {
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
	public static function test() {
		return true;
	}
}
