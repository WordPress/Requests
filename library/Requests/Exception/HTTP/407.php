<?php
/**
 * Exception for 407 Proxy Authentication Required responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 407 Proxy Authentication Required responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_407 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 407;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Proxy Authentication Required';
}
