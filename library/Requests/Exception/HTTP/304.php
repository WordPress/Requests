<?php
/**
 * Exception for 304 Not Modified responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 304 Not Modified responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_304 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 304;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Not Modified';
}
