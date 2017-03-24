<?php
namespace Rmccue\Requests\Exception\Http;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 409 Conflict responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 409 Conflict responses
 *
 * @package Rmccue\Requests
 */
class Response409 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 409;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Conflict';
}