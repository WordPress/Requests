<?php
namespace Rmccue\Requests\Exception\Http;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 414 Request-URI Too Large responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 414 Request-URI Too Large responses
 *
 * @package Rmccue\Requests
 */
class Response414 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 414;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Request-URI Too Large';
}