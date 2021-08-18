<?php
/**
 * Exception for 410 Gone responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 410 Gone responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_410 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 410;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Gone';
}
