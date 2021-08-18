<?php
/**
 * Exception for 416 Requested Range Not Satisfiable responses
 *
 * @package Requests
 */

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 416 Requested Range Not Satisfiable responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_416 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 416;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Requested Range Not Satisfiable';
}
