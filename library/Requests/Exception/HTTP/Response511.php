<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 511 Network Authentication Required responses
 *
 * @see https://tools.ietf.org/html/rfc6585
 * @package Rmccue\Requests
 */

/**
 * Exception for 511 Network Authentication Required responses
 *
 * @see https://tools.ietf.org/html/rfc6585
 * @package Rmccue\Requests
 */
class Response511 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 511;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Network Authentication Required';
}