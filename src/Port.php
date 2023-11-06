<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;

/**
 * Port utilities for Requests
 *
 * Find the correct port depending on the Request type.
 *
 * @package Requests\Utilities
 * @since   2.0.0
 */
final class Port {

	/**
	 * Port to use with Acap requests.
	 *
	 * @var int
	 */
	const ACAP = 674;

	/**
	 * Port to use with Dictionary requests.
	 *
	 * @var int
	 */
	const DICT = 2628;

	/**
	 * Port to use with HTTP requests.
	 *
	 * @var int
	 */
	const HTTP = 80;

	/**
	 * Port to use with HTTP over SSL requests.
	 *
	 * @var int
	 */
	const HTTPS = 443;

	/**
	 * Retrieve the port number to use.
	 *
	 * @param string $type Request type.
	 *                     The following requests types are supported:
	 *                     'acap', 'dict', 'http' and 'https'.
	 *
	 * @return int
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When a non-string input has been passed.
	 * @throws \WpOrg\Requests\Exception                 When a non-supported port is requested ('portnotsupported').
	 */
	public static function get($type) {
		if (!is_string($type)) {
			throw InvalidArgument::create(1, '$type', 'string', gettype($type));
		}

		$type = strtoupper($type);
		if (!defined("self::{$type}")) {
			$message = sprintf('Invalid port type (%s) passed', $type);
			throw new Exception($message, 'portnotsupported');
		}

		return constant("self::{$type}");
	}
}
