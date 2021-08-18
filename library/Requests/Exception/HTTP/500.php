<?php
/**
 * Exception for 500 Internal Server Error responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 500 Internal Server Error responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_500 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 500;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Internal Server Error';
}
