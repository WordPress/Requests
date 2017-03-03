<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 415 Unsupported Media Type responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 415 Unsupported Media Type responses
 *
 * @package Rmccue\Requests
 */
class Response415 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 415;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unsupported Media Type';
}