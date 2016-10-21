<?php
/**
 * Digest Authentication provider
 *
 * @package Requests
 * @subpackage Authentication
 */

/**
 * Digest Authentication provider
 *
 * Provides a handler for Digest HTTP authentication via the Authorization
 * header.
 *
 * @package Requests
 * @subpackage Authentication
 */

class Requests_Auth_Digest extends Requests_Auth_Basic {

	/**
	 * Set cURL parameters before the data is sent
	 *
	 * @param resource $handle cURL resource
	 */
	public function curl_before_send( &$handle ) {
		curl_setopt( $handle, CURLOPT_USERPWD, $this->getAuthString() );
		curl_setopt( $handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY ); //CURLAUTH_ANY work with Wowza RESTful
	}
}
