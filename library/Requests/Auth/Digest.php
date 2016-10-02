<?php

/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 10/2/16
 * Time: 11:40 AM
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