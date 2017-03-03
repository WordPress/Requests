<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 503 Service Unavailable responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 503 Service Unavailable responses
 *
 * @package Rmccue\Requests
 */
class Response503 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 503;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Service Unavailable';
}