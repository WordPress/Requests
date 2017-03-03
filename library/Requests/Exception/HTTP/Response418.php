<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 418 I'm A Teapot responses
 *
 * @see https://tools.ietf.org/html/rfc2324
 * @package Rmccue\Requests
 */

/**
 * Exception for 418 I'm A Teapot responses
 *
 * @see https://tools.ietf.org/html/rfc2324
 * @package Rmccue\Requests
 */
class Response418 extends HTTP {
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