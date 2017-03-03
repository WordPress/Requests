<?php
namespace Rmccue\Requests\Exception\Http;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 400 Bad Request responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 400 Bad Request responses
 *
 * @package Rmccue\Requests
 */
class Response400 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 400;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Bad Request';
}