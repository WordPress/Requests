<?php
/**
 * Exception for 404 Not Found responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 404 Not Found responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_404 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 404;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Not Found';
}
