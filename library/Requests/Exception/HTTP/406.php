<?php
/**
 * Exception for 406 Not Acceptable responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 406 Not Acceptable responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_406 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 406;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Not Acceptable';
}
