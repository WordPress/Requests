<?php
/**
 * Exception for 503 Service Unavailable responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 503 Service Unavailable responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_503 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 503;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Service Unavailable';
}
