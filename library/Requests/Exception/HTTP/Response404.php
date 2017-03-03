<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 404 Not Found responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 404 Not Found responses
 *
 * @package Rmccue\Requests
 */
class Response404 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 404;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Not Found';
}