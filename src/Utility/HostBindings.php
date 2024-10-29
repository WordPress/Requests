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
 *
 * @package Requests\Utilities
 * @since   2.x.x
 */
final class HostBindings {

    /**
     * Array of host bindings.
     *
     * @var array<string, array<string>>
     */
    private $host_bindings;

    public function __construct($host_bindings) {
        if (is_array($host_bindings) === false) {
            throw InvalidArgument::create(1, '$host_bindings', 'array', gettype($host_bindings));
        }

        $this->host_bindings = $this->validate($host_bindings);
    }

    /**
     * Validate a passed-in host bindings array.
     *
     * @param array $host_bindings Host bindings to validate.
     * @return array<string, array<string>> Validated array of host bindings.
     */
    private function validate($host_bindings) {
        foreach ($host_bindings as $host => $ips) {
            if (is_string($host) === false) {
                throw InvalidArgument::create(1, '$host_bindings (host)', 'string', gettype($host));
            }

            if (is_array($ips) === false) {
                throw InvalidArgument::create(1, '$host_bindings (ip address array)', 'array', gettype($ips));
            }

            foreach ($ips as $ip) {
                if (is_string($ip) === false) {
                    throw InvalidArgument::create(1, '$host_bindings (ip address)', 'string', gettype($ip));
                }
            }
        }

        return $host_bindings;
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