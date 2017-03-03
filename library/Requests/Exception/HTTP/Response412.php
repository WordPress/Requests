<?php
namespace Rmccue\Requests\Exception\Http;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 412 Precondition Failed responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 412 Precondition Failed responses
 *
 * @package Rmccue\Requests
 */
class Response412 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 412;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Precondition Failed';
}