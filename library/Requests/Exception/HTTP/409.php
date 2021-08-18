<?php
/**
 * Exception for 409 Conflict responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 409 Conflict responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_409 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 409;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Conflict';
}
