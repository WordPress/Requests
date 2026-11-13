<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Utility;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;

/**
 * URL utilities for Requests.
 *
 * Splits URLs using the {@see \WpOrg\Requests\Iri} class and (re)builds them in a
 * consistent way, so all transports send the exact same URL for the same request.
 *
 * @package Requests\Utilities
 * @since   2.1.0
 */
final class Url {

	/**
	 * Parse a URL which can be requested.
	 *
	 * @since 2.1.0
	 *
	 * @param string|\Stringable|\WpOrg\Requests\Iri $url The URL to parse.
	 *
	 * @return \WpOrg\Requests\Iri A new Iri object. A passed Iri object is never modified.
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string, Stringable or Iri.
	 * @throws \WpOrg\Requests\Exception                 When the URL is not valid or has no host ('invalidurl').
	 */
	public static function parse($url) {
		if (InputValidator::is_string_or_stringable($url) === false) {
			throw InvalidArgument::create(1, '$url', 'string|Stringable', gettype($url));
		}

		$iri = ($url instanceof Iri) ? clone $url : new Iri($url);

		if (self::is_requestable($iri) === false) {
			throw new Exception('Invalid URL.', 'invalidurl', $url);
		}

		return $iri;
	}

	/**
	 * Build the absolute URL to request, with the (optional) data added to the query string.
	 *
	 * Any existing query string is preserved as-is, the data is appended to it.
	 * The fragment is removed, as it is never sent to the server.
	 *
	 * @since 2.1.0
	 *
	 * @param string|\Stringable|\WpOrg\Requests\Iri $url  The URL.
	 * @param array|string                           $data Optional. Data to add to the query string.
	 *                                                     Arrays are encoded using {@link https://www.php.net/http_build_query},
	 *                                                     strings are expected to be encoded already.
	 *                                                     Defaults to an empty array.
	 *
	 * @return string The URL, encoded for use in a request.
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string, Stringable or Iri.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $data argument is not an array or string.
	 * @throws \WpOrg\Requests\Exception                 When the URL is not valid or has no host ('invalidurl').
	 */
	public static function get_request_url($url, $data = []) {
		if (is_array($data) === false && is_string($data) === false) {
			throw InvalidArgument::create(2, '$data', 'array|string', gettype($data));
		}

		$iri = self::parse($url);

		$iri->query    = self::merge_query($iri->iquery, self::build_query($data));
		$iri->fragment = null;

		return $iri->uri;
	}

	/**
	 * Get the request-target (path and query string) to use in the request line.
	 *
	 * This is the "origin-form" as defined in RFC 9112, section 3.2.1.
	 *
	 * @since 2.1.0
	 *
	 * @param string|\Stringable|\WpOrg\Requests\Iri $url The URL.
	 *
	 * @return string The path, followed by the query string if the URL has one, encoded for use in a request.
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string, Stringable or Iri.
	 * @throws \WpOrg\Requests\Exception                 When the URL is not valid or has no host ('invalidurl').
	 */
	public static function get_request_target($url) {
		$iri = self::parse($url);

		$target        = new Iri();
		$target->path  = $iri->ipath;
		$target->query = $iri->iquery;

		return $target->uri;
	}

	/**
	 * Check whether an Iri object holds a URL a request can be sent to.
	 *
	 * @param \WpOrg\Requests\Iri $iri The parsed URL.
	 *
	 * @return bool
	 */
	private static function is_requestable(Iri $iri) {
		return $iri->is_valid() && is_string($iri->ihost) && $iri->ihost !== '';
	}

	/**
	 * Encode the data to add to the query string.
	 *
	 * @param array|string $data The data.
	 *
	 * @return string
	 */
	private static function build_query($data) {
		if (is_string($data)) {
			return $data;
		}

		return http_build_query($data, '', '&');
	}

	/**
	 * Append encoded data to an existing query string.
	 *
	 * The existing query string is not altered. A query string which is empty, or
	 * consists of only "0", is still a query string and will be preserved.
	 *
	 * @param string|null $query    The existing query string. Null when the URL has no query string.
	 * @param string      $addition The encoded data to append.
	 *
	 * @return string|null The combined query string. Null when there is no query string.
	 */
	private static function merge_query($query, $addition) {
		if ($addition === '') {
			return $query;
		}

		$separator = self::needs_separator($query) ? '&' : '';

		return $query . $separator . $addition;
	}

	/**
	 * Check whether a separator is needed before appending to a query string.
	 *
	 * @param string|null $query The existing query string.
	 *
	 * @return bool
	 */
	private static function needs_separator($query) {
		return $query !== null && $query !== '' && substr($query, -1) !== '&';
	}
}
