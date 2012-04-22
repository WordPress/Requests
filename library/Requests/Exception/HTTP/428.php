<?php
/**
 * Exception for 428 Precondition Required responses
 *
 * @package Requests
 */

/**
 * Exception for 428 Precondition Required responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_428 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 428;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Precondition Required';
}