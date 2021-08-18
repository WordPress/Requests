<?php
/**
 * Exception for 415 Unsupported Media Type responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 415 Unsupported Media Type responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_415 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 415;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unsupported Media Type';
}
