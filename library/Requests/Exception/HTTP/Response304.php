<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 304 Not Modified responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 304 Not Modified responses
 *
 * @package Rmccue\Requests
 */
class Response304 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 304;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Not Modified';
}