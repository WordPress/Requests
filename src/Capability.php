<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests;

/**
 * Capability interface declaring the known capabilities.
 *
 * This is used as the authoritative source for which capabilities can be queried.
 *
 * @package Requests\Utilities
 * @since   2.0.0
 */
interface Capability {

	/**
	 * Support for SSL.
	 *
	 * @var string
	 */
	const SSL = 'ssl';

	/**
	 * Collection of all capabilities supported in Requests.
	 *
	 * Note: this does not automatically mean that the capability will be supported for your chosen transport!
	 *
	 * @var string[]
	 */
	const ALL = [
		self::SSL,
	];
}
