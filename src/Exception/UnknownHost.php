<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Exception;

use InvalidArgumentException;

/**
 * Exception for an unknown host being requested via HostBindings.
 *
 * @package Requests\Exceptions
 * @since   2.x.x
 */
final class UnknownHost extends InvalidArgumentException {

    /**
     * Instantiate an UnknownHost exception for an unknown host that was
     * requested via HostBindings.
     *
     * @param string $host Unknown host that was requested.
     * @return self
     */
    public static function for_host($host) {
        $message = "Unknown host was requested from the host bindings collection: {$host}";

        return new self($message);
    }
}