<?php
namespace Rmccue\Requests\Exception\HTTP;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 416 Requested Range Not Satisfiable responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 416 Requested Range Not Satisfiable responses
 *
 * @package Rmccue\Requests
 */
class Response416 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 416;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Requested Range Not Satisfiable';
}