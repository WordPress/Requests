<?php
/**
 * Exception for 411 Length Required responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 411 Length Required responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_411 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 411;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Length Required';
}
