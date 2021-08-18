<?php
/**
 * Exception for 305 Use Proxy responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 305 Use Proxy responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_305 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 305;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Use Proxy';
}
