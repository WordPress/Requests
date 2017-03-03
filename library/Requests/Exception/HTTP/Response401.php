<?php
namespace Rmccue\Requests\Exception\Http;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 401 Unauthorized responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 401 Unauthorized responses
 *
 * @package Rmccue\Requests
 */
class Response401 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 401;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unauthorized';
}