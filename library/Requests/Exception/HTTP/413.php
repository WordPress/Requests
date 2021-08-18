<?php
/**
 * Exception for 413 Request Entity Too Large responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 413 Request Entity Too Large responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_413 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 413;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Request Entity Too Large';
}
