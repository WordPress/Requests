<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 505 HTTP Version Not Supported responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 505 HTTP Version Not Supported responses
 *
 * @package Rmccue\Requests
 */
class Response505 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 505;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'HTTP Version Not Supported';
}