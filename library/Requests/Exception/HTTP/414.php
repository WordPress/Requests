<?php
/**
 * Exception for 414 Request-URI Too Large responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 414 Request-URI Too Large responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_414 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 414;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Request-URI Too Large';
}
