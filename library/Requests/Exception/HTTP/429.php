<?php
/**
 * Exception for 429 Too Many Requests responses
 *
 * @see https://tools.ietf.org/html/draft-nottingham-http-new-status-04
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 429 Too Many Requests responses
 *
 * @see https://tools.ietf.org/html/draft-nottingham-http-new-status-04
 * @package Requests
 */
class Requests_Exception_HTTP_429 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 429;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Too Many Requests';
}
