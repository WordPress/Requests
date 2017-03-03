<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 406 Not Acceptable responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 406 Not Acceptable responses
 *
 * @package Rmccue\Requests
 */
class Response406 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 406;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Not Acceptable';
}