<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 504 Gateway Timeout responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 504 Gateway Timeout responses
 *
 * @package Rmccue\Requests
 */
class Response504 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 504;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Gateway Timeout';
}