<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 402 Payment Required responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 402 Payment Required responses
 *
 * @package Rmccue\Requests
 */
class Response402 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 402;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Payment Required';
}