<?php
/**
 * Exception for 401 Unauthorized responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 401 Unauthorized responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_401 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 401;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unauthorized';
}
