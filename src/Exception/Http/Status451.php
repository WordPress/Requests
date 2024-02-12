<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Exception\Http;

use WpOrg\Requests\Exception\Http;
use WpOrg\Requests\Utility\HttpStatus;

/**
 * Exception for 451 Unavailable For Legal Reasons responses
 *
 * @package Requests\Exceptions
 */
final class Status451 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var int
	 */
	protected $code = 451;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = HttpStatus::TEXT_451;
}
