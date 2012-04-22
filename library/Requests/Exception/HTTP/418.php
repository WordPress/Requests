<?php
/**
 * Exception for 418 I'm A Teapot responses
 *
 * @package Requests
 */

/**
 * Exception for 418 I'm A Teapot responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_418 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 418;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = "I'm A Teapot";
}