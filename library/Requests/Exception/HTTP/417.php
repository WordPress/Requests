<?php
/**
 * Exception for 417 Expectation Failed responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 417 Expectation Failed responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_417 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 417;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Expectation Failed';
}
