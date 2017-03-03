<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 305 Use Proxy responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 305 Use Proxy responses
 *
 * @package Rmccue\Requests
 */
class Response305 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 305;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Use Proxy';
}
