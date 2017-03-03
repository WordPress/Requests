<?php
namespace Rmccue\Requests\Exception\Http;

Use Rmccue\Requests\Exception\HTTP as HTTP;
/**
 * Exception for 306 Switch Proxy responses
 *
 * @package Rmccue\Requests
 */

/**
 * Exception for 306 Switch Proxy responses
 *
 * @package Rmccue\Requests
 */
class Response306 extends HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 306;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Switch Proxy';
}
