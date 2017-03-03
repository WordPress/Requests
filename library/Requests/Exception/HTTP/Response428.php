<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 428 Precondition Required responses
 *
 * @see https://tools.ietf.org/html/rfc6585
 * @package Rmccue\Requests
 */

/**
 * Exception for 428 Precondition Required responses
 *
 * @see https://tools.ietf.org/html/rfc6585
 * @package Rmccue\Requests
 */
class Response428 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 428;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Precondition Required';
}