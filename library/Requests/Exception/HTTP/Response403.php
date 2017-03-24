<?php
namespace Rmccue\Requests\Exception\Http;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 403 Forbidden responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 403 Forbidden responses
 *
 * @package Rmccue\Requests
 */
class Response403 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 403;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Forbidden';
}