<?php

class Requests_Auth_Basic implements Requests_Auth {
	public function __construct($args = null) {
		if (is_array($args)) {
			if (count($args) !== 2) {
				throw new Requests_Exception('Invalid number of arguments', 'authbasicbadargs');
			}

			list($this->user, $this->pass) = $args;
		}
	}

	public function before_request(&$url, &$headers, &$data, &$type, &$options) {
		// We don't need to touch anything here.
	}

	public function before_send($type, &$transport_data, $url, $headers, $data, $options) {
		switch ($type) {
			case 'curl':
				curl_setopt($transport_data, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($transport_data, CURLOPT_USERPWD, $this->getAuthString());
				break;
		}
	}

	public function getAuthString() {
		return $this->user . ':' . $this->pass;
	}
}