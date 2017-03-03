<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 405 Method Not Allowed responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 405 Method Not Allowed responses
 *
 * @package Rmccue\Requests
 */
class Response405 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 405;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Method Not Allowed';
}