<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 417 Expectation Failed responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 417 Expectation Failed responses
 *
 * @package Rmccue\Requests
 */
class Response417 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 417;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Expectation Failed';
}