<?php
/**
 * Exception for 402 Payment Required responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 402 Payment Required responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_402 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 402;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Payment Required';
}
