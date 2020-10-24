<?php

class RequestsTest_Mock_Transport implements Requests_Transport {
	public $code        = 200;
	public $chunked     = false;
	public $body        = 'Test Body';
	public $raw_headers = '';

	private static $messages = array(
		100 => '100 Continue',
		101 => '101 Switching Protocols',
		200 => '200 OK',
		201 => '201 Created',
		202 => '202 Accepted',
		203 => '203 Non-Authoritative Information',
		204 => '204 No Content',
		205 => '205 Reset Content',
		206 => '206 Partial Content',
		300 => '300 Multiple Choices',
		301 => '301 Moved Permanently',
		302 => '302 Found',
		303 => '303 See Other',
		304 => '304 Not Modified',
		305 => '305 Use Proxy',
		306 => '306 (Unused)',
		307 => '307 Temporary Redirect',
		400 => '400 Bad Request',
		401 => '401 Unauthorized',
		402 => '402 Payment Required',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		405 => '405 Method Not Allowed',
		406 => '406 Not Acceptable',
		407 => '407 Proxy Authentication Required',
		408 => '408 Request Timeout',
		409 => '409 Conflict',
		410 => '410 Gone',
		411 => '411 Length Required',
		412 => '412 Precondition Failed',
		413 => '413 Request Entity Too Large',
		414 => '414 Request-URI Too Long',
		415 => '415 Unsupported Media Type',
		416 => '416 Requested Range Not Satisfiable',
		417 => '417 Expectation Failed',
		418 => '418 I\'m a teapot',
		428 => '428 Precondition Required',
		429 => '429 Too Many Requests',
		431 => '431 Request Header Fields Too Large',
		500 => '500 Internal Server Error',
		501 => '501 Not Implemented',
		502 => '502 Bad Gateway',
		503 => '503 Service Unavailable',
		504 => '504 Gateway Timeout',
		505 => '505 HTTP Version Not Supported',
		511 => '511 Network Authentication Required',
	);

	public function request($url, $headers = array(), $data = array(), $options = array()) {
		$status    = isset(self::$messages[$this->code]) ? self::$messages[$this->code] : $this->code . ' unknown';
		$response  = "HTTP/1.0 $status\r\n";
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
		$responses = array();
		foreach ($requests as $id => $request) {
			$handler              = new self();
			$handler->code        = $request['options']['mock.code'];
			$handler->chunked     = $request['options']['mock.chunked'];
			$handler->body        = $request['options']['mock.body'];
			$handler->raw_headers = $request['options']['mock.raw_headers'];
			$responses[$id]       = $handler->request($request['url'], $request['headers'], $request['data'], $request['options']);

			if (!empty($options['mock.parse'])) {
				$request['options']['hooks']->dispatch('transport.internal.parse_response', array(&$responses[$id], $request));
				$request['options']['hooks']->dispatch('multiple.request.complete', array(&$responses[$id], $id));
			}
		}

		return $responses;
	}

	public static function test() {
		return true;
	}
}
