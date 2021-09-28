<?php
/**
 * Basic Authentication provider
 *
 * @package Requests
 * @subpackage Authentication
 */

namespace WpOrg\Requests\Auth;

use WpOrg\Requests\Auth;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Hooks;

/**
 * Basic Authentication provider
 *
 * Provides a handler for Basic HTTP authentication via the Authorization
 * header.
 *
 * @package Requests
 * @subpackage Authentication
 */
class Basic implements Auth {
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
	 * @throws \WpOrg\Requests\Exception On incorrect number of arguments (`authbasicbadargs`)
	 * @param array|null $args Array of user and password. Must have exactly two elements
	 */
	public function __construct($args = null) {
		if (is_array($args)) {
			if (count($args) !== 2) {
				throw new Exception('Invalid number of arguments', 'authbasicbadargs');
			}

			list($this->user, $this->pass) = $args;
		}
	}

	/**
	 * Register the necessary callbacks
	 *
	 * @see \WpOrg\Requests\Auth\Basic::curl_before_send()
	 * @see \WpOrg\Requests\Auth\Basic::fsockopen_header()
	 * @param \WpOrg\Requests\Hooks $hooks Hook system
	 */
	public function register(Hooks $hooks) {
		$hooks->register('curl.before_send', array($this, 'curl_before_send'));
		$hooks->register('fsockopen.after_headers', array($this, 'fsockopen_header'));
	}

	/**
	 * Set cURL parameters before the data is sent
	 *
	 * @param resource|\CurlHandle $handle cURL handle
	 */
	public function curl_before_send(&$handle) {
		curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($handle, CURLOPT_USERPWD, $this->getAuthString());
	}

	/**
	 * Add extra headers to the request before sending
	 *
	 * @param string $out HTTP header string
	 */
	public function fsockopen_header(&$out) {
		$out .= sprintf("Authorization: Basic %s\r\n", base64_encode($this->getAuthString()));
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
