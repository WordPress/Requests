<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Utility;

use ArrayAccess;
use CurlHandle;
use Traversable;

/**
 * Input validation utilities.
 *
 * @package Requests\Utilities
 * @since   2.0.0
 */
final class InputValidator {

	/**
	 * Verify that a received input parameter is of type string or is "stringable".
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_string_or_stringable($input) {
		return is_string($input) || self::is_stringable_object($input);
	}

	/**
	 * Verify whether a received input parameter is usable as an integer array key.
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_numeric_array_key($input) {
		if (is_int($input)) {
			return true;
		}

		if (!is_string($input)) {
			return false;
		}

		return (bool) preg_match('`^-?[0-9]+$`', $input);
	}

	/**
	 * Verify whether a received input parameter is "stringable".
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_stringable_object($input) {
		return is_object($input) && method_exists($input, '__toString');
	}

	/**
	 * Verify whether a received input parameter is _accessible as if it were an array_.
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function has_array_access($input) {
		return is_array($input) || $input instanceof ArrayAccess;
	}

	/**
	 * Verify whether a received input parameter is "iterable".
	 *
	 * @internal The PHP native `is_iterable()` function was only introduced in PHP 7.1
	 * and this library still supports PHP 5.6.
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_iterable($input) {
		return is_array($input) || $input instanceof Traversable;
	}

	/**
	 * Verify whether a received input parameter is a Curl handle.
	 *
	 * The PHP Curl extension worked with resources prior to PHP 8.0 and with
	 * an instance of the `CurlHandle` class since PHP 8.0.
	 * {@link https://www.php.net/manual/en/migration80.incompatible.php#migration80.incompatible.resource2object}
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_curl_handle($input) {
		if (is_resource($input)) {
			return get_resource_type($input) === 'curl';
		}

		if (is_object($input)) {
			return $input instanceof CurlHandle;
		}

		return false;
	}

	/**
	 * Verify that a received input parameter is a valid "token name" according to the
	 * specification in RFC 2616 (HTTP/1.1).
	 *
	 * The short version is: 1 or more ASCII characters, CTRL chars and separators not allowed.
	 * For the long version, see the specs in the RFC.
	 *
	 * @link https://datatracker.ietf.org/doc/html/rfc2616#section-2.2
	 *
	 * @since 2.1.0
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return bool
	 */
	public static function is_valid_rfc2616_token($input) {
		if (!is_int($input) && !is_string($input)) {
			return false;
		}

		return preg_match('@^[0-9A-Za-z!#$%&\'*+.^_`|~-]+$@', $input) === 1;
	}
}
