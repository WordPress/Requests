<?php
/**
 * Exception for 400 Bad Request responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 400 Bad Request responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_400 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 400;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Bad Request';
}
