<?php
/**
 * Base HTTP transport
 *
 * @package Requests
 */

/**
 * Base HTTP transport
 *
 * @package Requests
 */
interface Requests_Transport {
	/**
	 * Perform a request
	 *
	 * @param string $url URL to request
	 * @param array $headers Associative array of request headers
	 * @param string|array $data Data to send either as the POST body, or as parameters in the URL for a GET/HEAD
	 * @return string Raw HTTP result
	 */
	public function request($url, $headers = array(), $data = array(), $options = array());

	/**
	 * Self-test whether the transport can be used
	 * @return bool
	 */
	public static function test();
}