<?php

namespace WpOrg\Requests\Tests\Fixtures;

use WpOrg\Requests\Transport;
use WpOrg\Requests\Utility\HttpStatus;

final class TransportMock implements Transport {
	public $code        = 200;
	public $chunked     = false;
	public $body        = 'Test Body';
	public $raw_headers = '';

	public function request($url, $headers = [], $data = [], $options = []) {
		$text      = HttpStatus::is_valid_code($this->code) ? HttpStatus::get_text($this->code) : 'unknown';
		$response  = "HTTP/1.0 {$this->code} {$text}\r\n";
		$response .= "Content-Type: text/plain\r\n";
		if ($this->chunked) {
			$response .= "Transfer-Encoding: chunked\r\n";
		}

		$response .= $this->raw_headers;
		$response .= "Connection: close\r\n\r\n";
		$response .= $this->body;
		return $response;
	}

	public function request_multiple($requests, $options) {
		$responses = [];
		foreach ($requests as $id => $request) {
			$handler              = new self();
			$handler->code        = $request['options']['mock.code'];
			$handler->chunked     = $request['options']['mock.chunked'];
			$handler->body        = $request['options']['mock.body'];
			$handler->raw_headers = $request['options']['mock.raw_headers'];
			$responses[$id]       = $handler->request($request['url'], $request['headers'], $request['data'], $request['options']);

			if (!empty($options['mock.parse'])) {
				$request['options']['hooks']->dispatch('transport.internal.parse_response', [&$responses[$id], $request]);
				$request['options']['hooks']->dispatch('multiple.request.complete', [&$responses[$id], $id]);
			}
		}

		return $responses;
	}

	public static function test($capabilities = []) {
		return true;
	}
}
