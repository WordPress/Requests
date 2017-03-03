<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 407 Proxy Authentication Required responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 407 Proxy Authentication Required responses
 *
 * @package Rmccue\Requests
 */
class Response407 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 407;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Proxy Authentication Required';
}