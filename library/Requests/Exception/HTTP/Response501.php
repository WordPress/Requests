<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 501 Not Implemented responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 501 Not Implemented responses
 *
 * @package Rmccue\Requests
 */
class Response501 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 501;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Not Implemented';
}