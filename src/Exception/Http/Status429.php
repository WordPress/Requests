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
 * Exception for 429 Too Many Requests responses
 *
 * @link https://tools.ietf.org/html/draft-nottingham-http-new-status-04
 *
 * @package Requests\Exceptions
 */
final class Status429 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var int
	 */
	protected $code = 429;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = HttpStatus::TEXT_429;
}
