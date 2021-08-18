<?php
/**
 * Exception for 502 Bad Gateway responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 502 Bad Gateway responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_502 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 502;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Bad Gateway';
}
