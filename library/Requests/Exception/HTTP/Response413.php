<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 413 Request Entity Too Large responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 413 Request Entity Too Large responses
 *
 * @package Rmccue\Requests
 */
class Response413 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 413;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Request Entity Too Large';
}