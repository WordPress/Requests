<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Exception;

use RangeException;

/**
 * Exception for a missing IP address in the host bindings.
 *
 * @package Requests\Exceptions
 * @since   2.x.x
 */
final class MissingIpAddress extends RangeException {

	/**
	 * Instantiate a MissingIpAddress exception for a missing IP address in the host bindings.
	 *
	 * @param string $host Host that was requested.
	 * @return self
	 */
	public static function for_host($host) {
		$message = "No IP address was found for host: {$host}";

		return new self($message);
	}
}
