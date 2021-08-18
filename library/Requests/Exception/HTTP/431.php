<?php
/**
 * Exception for 431 Request Header Fields Too Large responses
 *
 * @see https://tools.ietf.org/html/rfc6585
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 431 Request Header Fields Too Large responses
 *
 * @see https://tools.ietf.org/html/rfc6585
 * @package Requests
 */
class Requests_Exception_HTTP_431 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 431;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Request Header Fields Too Large';
}
