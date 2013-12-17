<?php

/**
 * Digest Authentication provider
 *
 * Provides a handler for Basic HTTP authentication via the Authorization
 * header.
 *
 * @package Requests
 * @subpackage Authentication
 */
class Requests_Auth_Digest implements Requests_Auth {
	/**
	 * Username
	 *
	 * @var string
	 */
	public $user;

	/**
	 * Password
	 *
	 * @var string
	 */
	public $pass;

	/**
	 * Constructor
	 *
	 * @throws Requests_Exception On incorrect number of arguments (`authdigestbadargs`)
	 * @param array|null $args Array of user and password. Must have exactly two elements
	 */
	public function __construct($args = null) {
		if (is_array($args)) {
			if (count($args) !== 2) {
				throw new Requests_Exception('Invalid number of arguments', 'authdigestbadargs');
			}

			list($this->user, $this->pass) = $args;
		}
	}

	/**
	 * Register the necessary callbacks
	 *
	 * @see curl_before_send
	 * @see fsockopen_header
	 * @param Requests_Hooks $hooks Hook system
	 */
	public function register(Requests_Hooks &$hooks) {
		$hooks->register('curl.before_send', array(&$this, 'curl_before_send'));
		$hooks->register('fsockopen.after_headers', array(&$this, 'fsockopen_header'));
	}

	/**
	 * Set cURL parameters before the data is sent
	 *
	 * @param resource $handle cURL resource
	 */
	public function curl_before_send(&$handle) {
		curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
		curl_setopt($handle, CURLOPT_USERPWD, $this->getAuthString());
	}

	/**
	 * Add extra headers to the request before sending
	 *
	 * @param string $out HTTP header string
	 */
	public function fsockopen_header(&$out) {
		$out .= "Authorization: Digest " . base64_encode($this->getAuthString()) . "\r\n";
	}

	/**
	 * Get the authentication string (user:pass)
	 *
	 * @return string
	 */
	public function getAuthString() {
		return $this->user . ':' . $this->pass;
	}
}