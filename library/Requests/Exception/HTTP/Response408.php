<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 408 Request Timeout responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 408 Request Timeout responses
 *
 * @package Rmccue\Requests
 */
class Response408 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 408;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Request Timeout';
}