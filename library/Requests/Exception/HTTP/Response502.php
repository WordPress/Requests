<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 502 Bad Gateway responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 502 Bad Gateway responses
 *
 * @package Rmccue\Requests
 */
class Response502 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 502;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Bad Gateway';
}