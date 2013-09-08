<?php
/**
 * HTTP Proxy connection interface
 *
 * @package Requests
 * @subpackage Proxy
 * @since 1.6
 */

/**
 * HTTP Proxy connection interface
 *
 * Provides a handler for connection via an HTTP proxy
 *
 * @package Requests
 * @subpackage Proxy
 * @since 1.6
 */
class Requests_Proxy_HTTP implements Requests_Proxy {
	/**
	 * Proxy host and port
	 *
	 * Notation: "host:port" (eg 127.0.0.1:8080 or someproxy.com:3128)
	 *
	 * @var string
	 */
	public $proxy;

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
	 * @throws Requests_Exception On incorrect number of arguments (`authbasicbadargs`)
	 * @param array|null $args Array of user and password. Must have exactly two elements
	 */
	public function __construct($args = null) {
		if( is_string( $args ) {
			$this->proxy = $args;
		} elseif( is_array( $args ) && count( $args ) == 3 ) {
			list( $this->proxy, $this->user, $this->pass ) = $args;
		} else {
			throw new Requests_Exception( 'Invalid number of arguments', 'proxyhttpbadargs');
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
		curl_setopt( &$handle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP );
		curl_setopt( &$handle, CURLOPT_PROXY, $this->proxy );
		
		if ( isset( $this->user ) && isset( $this->pass ) ) {
			curl_setopt( &$handle, CURLOPT_PROXYAUTH, CURLAUTH_ANY );
			curl_setopt( &$handle, CURLOPT_PROXYUSERPWD, $this->getAuthString() );
	}

	/**
	 * Add extra headers to the request before sending
	 *
	 * @param string $out HTTP header string
	 */
	public function fsockopen_header(&$out) {
		$out .= "Authorization: Basic " . base64_encode($this->getAuthString()) . "\r\n";
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