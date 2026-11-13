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
 * Validates URLs using the {@see \WpOrg\Requests\Iri} class and builds the URL and
 * request-target to send in a consistent way, so all transports send the exact same
 * URL for the same request.
 *
 * Only the query string is ever changed. The rest of the URL is sent exactly as passed,
 * as it has already been normalized by {@see \WpOrg\Requests\Requests::request()},
 * unless IDN handling was explicitly disabled.
 *
 * @package Requests\Utilities
 * @since   2.1.0
 */
final class Url {

	/**
	 * Regular expression to split a URL into its components.
	 *
	 * This is the regular expression from RFC 3986, appendix B, as also used by
	 * {@see \WpOrg\Requests\Iri::parse_iri()}, which does not change any characters.
	 *
	 * @link https://www.rfc-editor.org/rfc/rfc3986#appendix-B
	 *
	 * @var string
	 */
	const COMPONENTS_REGEX = '`^(?P<origin>(?:[^:/?#]+:)?(?://[^/?#]*)?)(?P<path>[^?#]*)(?:\?(?P<query>[^#]*))?`';

	/**
	 * Characters which are trimmed from the URL, in the same way as {@see \WpOrg\Requests\Iri}.
	 *
	 * @var string
	 */
	const TRIM_CHARS = "\x20\x09\x0A\x0C\x0D";

	/**
	 * Parse a URL which can be requested.
	 *
	 * @since 2.1.0
	 *
	 * @param string|\Stringable $url The URL to parse.
	 *
	 * @return \WpOrg\Requests\Iri A new Iri object. A passed Iri object is never modified.
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string or Stringable.
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
	 * @param string|\Stringable $url  The URL.
	 * @param array|string       $data Optional. Data to add to the query string.
	 *                                 Arrays are encoded using {@link https://www.php.net/http_build_query},
	 *                                 strings are expected to be encoded already.
	 *                                 Defaults to an empty array.
	 *
	 * @return string The URL to request.
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string or Stringable.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $data argument is not an array or string.
	 * @throws \WpOrg\Requests\Exception                 When the URL is not valid or has no host ('invalidurl').
	 */
	public static function get_request_url($url, $data = []) {
		$components = self::split($url, $data);

		return $components['origin'] . self::build_target($components);
	}

	/**
	 * Get the request-target (path and query string) to use in the request line,
	 * with the (optional) data added to the query string.
	 *
	 * This is the "origin-form" as defined in RFC 9112, section 3.2.1.
	 *
	 * @since 2.1.0
	 *
	 * @param string|\Stringable $url  The URL.
	 * @param array|string       $data Optional. Data to add to the query string.
	 *                                 See {@see \WpOrg\Requests\Utility\Url::get_request_url()}.
	 *                                 Defaults to an empty array.
	 *
	 * @return string The path, followed by the query string if there is one.
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string or Stringable.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $data argument is not an array or string.
	 * @throws \WpOrg\Requests\Exception                 When the URL is not valid or has no host ('invalidurl').
	 */
	public static function get_request_target($url, $data = []) {
		return self::build_target(self::split($url, $data));
	}

	/**
	 * Validate the URL and split it into the components needed to build a request,
	 * with the data merged into the query string.
	 *
	 * @param string|\Stringable $url  The URL.
	 * @param array|string       $data Data to add to the query string.
	 *
	 * @return array{origin: string, path: string, query: string|null}
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $url argument is not a string or Stringable.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $data argument is not an array or string.
	 * @throws \WpOrg\Requests\Exception                 When the URL is not valid or has no host ('invalidurl').
	 */
	private static function split($url, $data) {
		if (is_array($data) === false && is_string($data) === false) {
			throw InvalidArgument::create(2, '$data', 'array|string', gettype($data));
		}

		self::parse($url);

		preg_match(self::COMPONENTS_REGEX, trim((string) $url, self::TRIM_CHARS), $matches);

		return [
			'origin' => $matches['origin'],
			'path'   => $matches['path'],
			'query'  => self::merge_query(isset($matches['query']) ? $matches['query'] : null, self::build_query($data)),
		];
	}

	/**
	 * Build the request-target from the URL components.
	 *
	 * @param array{origin: string, path: string, query: string|null} $components The URL components.
	 *
	 * @return string
	 */
	private static function build_target(array $components) {
		$path = ($components['path'] === '') ? '/' : $components['path'];

		if ($components['query'] === null) {
			return $path;
		}

		return $path . '?' . $components['query'];
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
