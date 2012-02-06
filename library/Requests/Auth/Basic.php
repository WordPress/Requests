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

	public function register(Requests_Hooks &$hooks) {
		$hooks->register('curl.before_send', array(&$this, 'curl_before_send'));
		$hooks->register('fsockopen.after_headers', array(&$this, 'fsockopen_header'));
	}

	public function curl_before_send(&$handle, $url, $headers, $data, $options) {
		curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($handle, CURLOPT_USERPWD, $this->getAuthString());
	}

	public function fsockopen_header(&$out) {
		$out .= "Authorization: Basic " . base64_encode($this->getAuthString()) . "\r\n";
	}

	public function getAuthString() {
		return $this->user . ':' . $this->pass;
	}
}