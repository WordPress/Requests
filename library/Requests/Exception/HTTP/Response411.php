<?php
namespace Rmccue\Requests\Exception\Http;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 411 Length Required responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 411 Length Required responses
 *
 * @package Rmccue\Requests
 */
class Response411 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 411;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Length Required';
}