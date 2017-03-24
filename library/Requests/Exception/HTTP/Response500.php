<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 500 Internal Server Error responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 500 Internal Server Error responses
 *
 * @package Rmccue\Requests
 */
class Response500 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 500;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Internal Server Error';
}