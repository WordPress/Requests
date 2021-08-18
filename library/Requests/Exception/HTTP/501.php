<?php
/**
 * Exception for 501 Not Implemented responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 501 Not Implemented responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_501 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 501;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Not Implemented';
}
