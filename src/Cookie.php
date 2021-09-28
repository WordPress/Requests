<?php
/**
 * Cookie storage object
 *
 * @package Requests
 * @subpackage Cookies
 */

namespace WpOrg\Requests;

use WpOrg\Requests\Iri;
use WpOrg\Requests\Response\Headers;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * Cookie storage object
 *
 * @package Requests
 * @subpackage Cookies
 */
class Cookie {
	/**
	 * Cookie name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Cookie value.
	 *
	 * @var string
	 */
	public $value;

	/**
	 * Cookie attributes
	 *
	 * Valid keys are (currently) path, domain, expires, max-age, secure and
	 * httponly.
	 *
	 * @var \WpOrg\Requests\Utility\CaseInsensitiveDictionary|array Array-like object
	 */
	public $attributes = array();

	/**
	 * Cookie flags
	 *
	 * Valid keys are (currently) creation, last-access, persistent and
	 * host-only.
	 *
	 * @var array
	 */
	public $flags = array();

	/**
	 * Reference time for relative calculations
	 *
	 * This is used in place of `time()` when calculating Max-Age expiration and
	 * checking time validity.
	 *
	 * @var int
	 */
	public $reference_time = 0;

	/**
	 * Create a new cookie object
	 *
	 * @param string $name
	 * @param string $value
	 * @param array|\WpOrg\Requests\Utility\CaseInsensitiveDictionary $attributes Associative array of attribute data
	 */
	public function __construct($name, $value, $attributes = array(), $flags = array(), $reference_time = null) {
		$this->name       = $name;
		$this->value      = $value;
		$this->attributes = $attributes;
		$default_flags    = array(
			'creation'    => time(),
			'last-access' => time(),
			'persistent'  => false,
			'host-only'   => true,
		);
		$this->flags      = array_merge($default_flags, $flags);

		$this->reference_time = time();
		if ($reference_time !== null) {
			$this->reference_time = $reference_time;
		}

		$this->normalize();
	}

	/**
	 * Get the cookie value
	 *
	 * Attributes and other data can be accessed via methods.
	 */
	public function __toString() {
		return $this->value;
	}

	/**
	 * Check if a cookie is expired.
	 *
	 * Checks the age against $this->reference_time to determine if the cookie
	 * is expired.
	 *
	 * @return boolean True if expired, false if time is valid.
	 */
	public function is_expired() {
		// RFC6265, s. 4.1.2.2:
		// If a cookie has both the Max-Age and the Expires attribute, the Max-
		// Age attribute has precedence and controls the expiration date of the
		// cookie.
		if (isset($this->attributes['max-age'])) {
			$max_age = $this->attributes['max-age'];
			return $max_age < $this->reference_time;
		}

		if (isset($this->attributes['expires'])) {
			$expires = $this->attributes['expires'];
			return $expires < $this->reference_time;
		}

		return false;
	}

	/**
	 * Check if a cookie is valid for a given URI
	 *
	 * @param \WpOrg\Requests\Iri $uri URI to check
	 * @return boolean Whether the cookie is valid for the given URI
	 */
	public function uri_matches(Iri $uri) {
		if (!$this->domain_matches($uri->host)) {
			return false;
		}

		if (!$this->path_matches($uri->path)) {
			return false;
		}

		return empty($this->attributes['secure']) || $uri->scheme === 'https';
	}

	/**
	 * Check if a cookie is valid for a given domain
	 *
	 * @param string $domain Domain to check
	 * @return boolean Whether the cookie is valid for the given domain
	 */
	public function domain_matches($domain) {
		if (!isset($this->attributes['domain'])) {
			// Cookies created manually; cookies created by Requests will set
			// the domain to the requested domain
			return true;
		}

		$cookie_domain = $this->attributes['domain'];
		if ($cookie_domain === $domain) {
			// The cookie domain and the passed domain are identical.
			return true;
		}

		// If the cookie is marked as host-only and we don't have an exact
		// match, reject the cookie
		if ($this->flags['host-only'] === true) {
			return false;
		}

		if (strlen($domain) <= strlen($cookie_domain)) {
			// For obvious reasons, the cookie domain cannot be a suffix if the passed domain
			// is shorter than the cookie domain
			return false;
		}

		if (substr($domain, -1 * strlen($cookie_domain)) !== $cookie_domain) {
			// The cookie domain should be a suffix of the passed domain.
			return false;
		}

		$prefix = substr($domain, 0, strlen($domain) - strlen($cookie_domain));
		if (substr($prefix, -1) !== '.') {
			// The last character of the passed domain that is not included in the
			// domain string should be a %x2E (".") character.
			return false;
		}

		// The passed domain should be a host name (i.e., not an IP address).
		return !preg_match('#^(.+\.)\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $domain);
	}

	/**
	 * Check if a cookie is valid for a given path
	 *
	 * From the path-match check in RFC 6265 section 5.1.4
	 *
	 * @param string $request_path Path to check
	 * @return boolean Whether the cookie is valid for the given path
	 */
	public function path_matches($request_path) {
		if (empty($request_path)) {
			// Normalize empty path to root
			$request_path = '/';
		}

		if (!isset($this->attributes['path'])) {
			// Cookies created manually; cookies created by Requests will set
			// the path to the requested path
			return true;
		}

		$cookie_path = $this->attributes['path'];

		if ($cookie_path === $request_path) {
			// The cookie-path and the request-path are identical.
			return true;
		}

		if (strlen($request_path) > strlen($cookie_path) && substr($request_path, 0, strlen($cookie_path)) === $cookie_path) {
			if (substr($cookie_path, -1) === '/') {
				// The cookie-path is a prefix of the request-path, and the last
				// character of the cookie-path is %x2F ("/").
				return true;
			}

			if (substr($request_path, strlen($cookie_path), 1) === '/') {
				// The cookie-path is a prefix of the request-path, and the
				// first character of the request-path that is not included in
				// the cookie-path is a %x2F ("/") character.
				return true;
			}
		}

		return false;
	}

	/**
	 * Normalize cookie and attributes
	 *
	 * @return boolean Whether the cookie was successfully normalized
	 */
	public function normalize() {
		foreach ($this->attributes as $key => $value) {
			$orig_value = $value;
			$value      = $this->normalize_attribute($key, $value);
			if ($value === null) {
				unset($this->attributes[$key]);
				continue;
			}

			if ($value !== $orig_value) {
				$this->attributes[$key] = $value;
			}
		}

		return true;
	}

	/**
	 * Parse an individual cookie attribute
	 *
	 * Handles parsing individual attributes from the cookie values.
	 *
	 * @param string $name Attribute name
	 * @param string|boolean $value Attribute value (string value, or true if empty/flag)
	 * @return mixed Value if available, or null if the attribute value is invalid (and should be skipped)
	 */
	protected function normalize_attribute($name, $value) {
		switch (strtolower($name)) {
			case 'expires':
				// Expiration parsing, as per RFC 6265 section 5.2.1
				if (is_int($value)) {
					return $value;
				}

				$expiry_time = strtotime($value);
				if ($expiry_time === false) {
					return null;
				}

				return $expiry_time;

			case 'max-age':
				// Expiration parsing, as per RFC 6265 section 5.2.2
				if (is_int($value)) {
					return $value;
				}

				// Check that we have a valid age
				if (!preg_match('/^-?\d+$/', $value)) {
					return null;
				}

				$delta_seconds = (int) $value;
				if ($delta_seconds <= 0) {
					$expiry_time = 0;
				}
				else {
					$expiry_time = $this->reference_time + $delta_seconds;
				}

				return $expiry_time;

			case 'domain':
				// Domains are not required as per RFC 6265 section 5.2.3
				if (empty($value)) {
					return null;
				}

				// Domain normalization, as per RFC 6265 section 5.2.3
				if ($value[0] === '.') {
					$value = substr($value, 1);
				}

				return $value;

			default:
				return $value;
		}
	}

	/**
	 * Format a cookie for a Cookie header
	 *
	 * This is used when sending cookies to a server.
	 *
	 * @return string Cookie formatted for Cookie header
	 */
	public function format_for_header() {
		return sprintf('%s=%s', $this->name, $this->value);
	}

	/**
	 * Format a cookie for a Set-Cookie header
	 *
	 * This is used when sending cookies to clients. This isn't really
	 * applicable to client-side usage, but might be handy for debugging.
	 *
	 * @return string Cookie formatted for Set-Cookie header
	 */
	public function format_for_set_cookie() {
		$header_value = $this->format_for_header();
		if (!empty($this->attributes)) {
			$parts = array();
			foreach ($this->attributes as $key => $value) {
				// Ignore non-associative attributes
				if (is_numeric($key)) {
					$parts[] = $value;
				}
				else {
					$parts[] = sprintf('%s=%s', $key, $value);
				}
			}

			$header_value .= '; ' . implode('; ', $parts);
		}
		return $header_value;
	}

	/**
	 * Parse a cookie string into a cookie object
	 *
	 * Based on Mozilla's parsing code in Firefox and related projects, which
	 * is an intentional deviation from RFC 2109 and RFC 2616. RFC 6265
	 * specifies some of this handling, but not in a thorough manner.
	 *
	 * @param string $cookie_header Cookie header value (from a Set-Cookie header)
	 * @return \WpOrg\Requests\Cookie Parsed cookie object
	 */
	public static function parse($cookie_header, $name = '', $reference_time = null) {
		$parts   = explode(';', $cookie_header);
		$kvparts = array_shift($parts);

		if (!empty($name)) {
			$value = $cookie_header;
		}
		elseif (strpos($kvparts, '=') === false) {
			// Some sites might only have a value without the equals separator.
			// Deviate from RFC 6265 and pretend it was actually a blank name
			// (`=foo`)
			//
			// https://bugzilla.mozilla.org/show_bug.cgi?id=169091
			$name  = '';
			$value = $kvparts;
		}
		else {
			list($name, $value) = explode('=', $kvparts, 2);
		}
		$name  = trim($name);
		$value = trim($value);

		// Attribute key are handled case-insensitively
		$attributes = new CaseInsensitiveDictionary();

		if (!empty($parts)) {
			foreach ($parts as $part) {
				if (strpos($part, '=') === false) {
					$part_key   = $part;
					$part_value = true;
				}
				else {
					list($part_key, $part_value) = explode('=', $part, 2);
					$part_value                  = trim($part_value);
				}

				$part_key              = trim($part_key);
				$attributes[$part_key] = $part_value;
			}
		}

		return new static($name, $value, $attributes, array(), $reference_time);
	}

	/**
	 * Parse all Set-Cookie headers from request headers
	 *
	 * @param \WpOrg\Requests\Response\Headers $headers Headers to parse from
	 * @param \WpOrg\Requests\Iri|null $origin URI for comparing cookie origins
	 * @param int|null $time Reference time for expiration calculation
	 * @return array
	 */
	public static function parse_from_headers(Headers $headers, Iri $origin = null, $time = null) {
		$cookie_headers = $headers->getValues('Set-Cookie');
		if (empty($cookie_headers)) {
			return array();
		}

		$cookies = array();
		foreach ($cookie_headers as $header) {
			$parsed = self::parse($header, '', $time);

			// Default domain/path attributes
			if (empty($parsed->attributes['domain']) && !empty($origin)) {
				$parsed->attributes['domain'] = $origin->host;
				$parsed->flags['host-only']   = true;
			}
			else {
				$parsed->flags['host-only'] = false;
			}

			$path_is_valid = (!empty($parsed->attributes['path']) && $parsed->attributes['path'][0] === '/');
			if (!$path_is_valid && !empty($origin)) {
				$path = $origin->path;

				// Default path normalization as per RFC 6265 section 5.1.4
				if (substr($path, 0, 1) !== '/') {
					// If the uri-path is empty or if the first character of
					// the uri-path is not a %x2F ("/") character, output
					// %x2F ("/") and skip the remaining steps.
					$path = '/';
				}
				elseif (substr_count($path, '/') === 1) {
					// If the uri-path contains no more than one %x2F ("/")
					// character, output %x2F ("/") and skip the remaining
					// step.
					$path = '/';
				}
				else {
					// Output the characters of the uri-path from the first
					// character up to, but not including, the right-most
					// %x2F ("/").
					$path = substr($path, 0, strrpos($path, '/'));
				}
				$parsed->attributes['path'] = $path;
			}

			// Reject invalid cookie domains
			if (!empty($origin) && !$parsed->domain_matches($origin->host)) {
				continue;
			}

			$cookies[$parsed->name] = $parsed;
		}

		return $cookies;
	}
}
