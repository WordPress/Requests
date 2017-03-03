<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 410 Gone responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 410 Gone responses
 *
 * @package Rmccue\Requests
 */
class Response410 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 410;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Gone';
}