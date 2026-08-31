<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Utility;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Exception\MissingIpAddress;
use WpOrg\Requests\Exception\UnknownHost;

/**
 * Value object to handle host bindings that are provided via the $options array.
 *
 * Each mapping maps a host URL string to an array of one or more IPv4/IPv6 addresses.
 * IP addresses are validated by default to prevent hostname injection attacks.
 *
 * Security Note:
 * - IP addresses are validated using filter_var(FILTER_VALIDATE_IP) by default
 * - Accepts valid IPv4 and IPv6 addresses (including private/localhost)
 * - Rejects hostnames, URLs, and invalid formats
 * - Validation can be disabled for pre-validated inputs (use with caution)
 *
 * @package Requests\Utilities
 * @since   2.x.x
 */
final class HostBindings {

	/**
	 * Enable IP address validation in the constructor (default, recommended).
	 *
	 * @since 2.x.x
	 *
	 * @var bool
	 */
	const VALIDATE_IPS = true;

	/**
	 * Skip IP address validation in the constructor.
	 *
	 * WARNING: Only use if you have already validated the IP addresses yourself,
	 * or need to use non-standard address formats. Skipping validation may allow
	 * hostname injection attacks if untrusted data is used.
	 *
	 * @since 2.x.x
	 *
	 * @var bool
	 */
	const SKIP_IP_VALIDATION = false;

	/**
	 * Array of host bindings.
	 *
	 * @var array<string, array<string>>
	 */
	private $host_bindings;

	/**
	 * Construct HostBindings object.
	 *
	 * @since 2.x.x
	 *
	 * @param array<string, array<string>> $host_bindings Host to IP address mappings.
	 * @param bool                          $validate_ips  Whether to validate IP address formats.
	 *                                                      Default: true (recommended).
	 *                                                      Set to false ONLY if you have already
	 *                                                      validated the IP addresses yourself.
	 *
	 * @throws InvalidArgument If parameters are invalid or IPs fail validation.
	 */
	public function __construct($host_bindings, $validate_ips = self::VALIDATE_IPS) {
		if (is_array($host_bindings) === false) {
			throw InvalidArgument::create(1, '$host_bindings', 'array', gettype($host_bindings));
		}

		if (is_bool($validate_ips) === false) {
			throw InvalidArgument::create(2, '$validate_ips', 'boolean', gettype($validate_ips));
		}

		$this->host_bindings = $this->validate($host_bindings, $validate_ips);
	}

	/**
	 * Validate and normalize a passed-in host bindings array.
	 *
	 * @since 2.x.x
	 *
	 * @param array<string,array<string>> $host_bindings Host bindings to validate.
	 * @param bool                        $validate_ips  Whether to validate IP formats.
	 *
	 * @return array<string,array<string>> Validated and normalized array of host bindings.
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument If validation fails.
	 */
	private function validate($host_bindings, $validate_ips) {
		$validated = [];

		foreach ($host_bindings as $host => $ips) {
			// Validate host key.
			if (is_string($host) === false || $host === '') {
				throw InvalidArgument::create(1, '$host_bindings (host)', 'non-empty string', gettype($host));
			}

			// Validate IPs array.
			if (is_array($ips) === false) {
				throw InvalidArgument::create(1, '$host_bindings (ip address array)', 'array', gettype($ips));
			}

			$validated_ips = [];
			foreach ($ips as $ip) {
				// Type check.
				if (is_string($ip) === false) {
					throw InvalidArgument::create(1, '$host_bindings (ip address)', 'string', gettype($ip));
				}

				// Normalize: trim whitespace.
				$ip_trimmed = trim($ip);
				// Validate: non-empty after trim.
				if ($ip_trimmed === '') {
					throw InvalidArgument::create(
						1,
						'$host_bindings (ip address value)',
						'non-empty IP address',
						'empty string provided'
					);
				}

				// Conditionally validate IP format.
				if ($validate_ips === true) {
					// Validate both IPv4 and IPv6, including private/localhost ranges.
					if (filter_var($ip_trimmed, FILTER_VALIDATE_IP) === false) {
						throw InvalidArgument::create(
							1,
							'$host_bindings (ip address value)',
							'valid IPv4 or IPv6 address',
							sprintf('invalid IP address format: "%s"', $ip)
						);
					}
				}

				$validated_ips[] = $ip_trimmed;
			}

			$validated[$host] = $validated_ips;
		}

		return $validated;
	}

	/**
	 * Check whether the host bindings have a mapping for a particular host.
	 *
	 * @param string $host Host to check for.
	 * @return bool Whether the provided host has a mapping in the host bindings.
	 */
	public function has_host($host) {
		if (is_string($host) === false) {
			throw InvalidArgument::create(1, '$host', 'string', gettype($host));
		}

		return array_key_exists($host, $this->host_bindings);
	}

	/**
	 * Get the first IP address mapping in the host bindings for a particular host.
	 *
	 * This throws an exception if the requested host does not have a mapping.
	 * This also throws an exception if the requested host has no IP addresses available.
	 *
	 * @param string $host Host to request a mapping for.
	 * @return string IP address mapping for the host.
	 * @throws UnknownHost If the requested host does not have a mapping.
	 * @throws MissingIpAddress If the requested host has no IP address available.
	 */
	public function get_first_ip_for_host($host) {
		if ($this->has_host($host) === false) {
			throw UnknownHost::for_host($host);
		}

		if (count($this->host_bindings[$host]) === 0) {
			throw MissingIpAddress::for_host($host);
		}

		return $this->host_bindings[$host][0];
	}

	/**
	 * Get all IP address mappings in the host bindings for a particular host.
	 *
	 * This throws an exception if the requested host does not have a mapping.
	 * Contrary to get_first_ip_for_host(), this method does not throw an exception
	 * if the host has no IP addresses available. It will simply return an empty array.
	 *
	 * @param string $host Host to request a mapping for.
	 * @return array<string> IP address mappings for the host.
	 * @throws UnknownHost If the requested host does not have a mapping.
	 */
	public function get_all_ips_for_host($host) {
		if ($this->has_host($host) === false) {
			throw UnknownHost::for_host($host);
		}

		return $this->host_bindings[$host];
	}
}
