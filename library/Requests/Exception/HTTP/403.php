<?php
/**
 * Exception for 403 Forbidden responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 403 Forbidden responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_403 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 403;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Forbidden';
}
