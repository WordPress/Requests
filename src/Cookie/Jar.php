<?php
/**
 * Cookie holder object
 *
 * @package Requests
 * @subpackage Cookies
 */

namespace WpOrg\Requests\Cookie;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use ReturnTypeWillChange;
use WpOrg\Requests\Cookie;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Hooker;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Response;

/**
 * Cookie holder object
 *
 * @package Requests
 * @subpackage Cookies
 */
class Jar implements ArrayAccess, IteratorAggregate {
	/**
	 * Actual item data
	 *
	 * @var array
	 */
	protected $cookies = array();

	/**
	 * Create a new jar
	 *
	 * @param array $cookies Existing cookie values
	 */
	public function __construct($cookies = array()) {
		$this->cookies = $cookies;
	}

	/**
	 * Normalise cookie data into a \WpOrg\Requests\Cookie
	 *
	 * @param string|\WpOrg\Requests\Cookie $cookie
	 * @return \WpOrg\Requests\Cookie
	 */
	public function normalize_cookie($cookie, $key = null) {
		if ($cookie instanceof Cookie) {
			return $cookie;
		}

		return Cookie::parse($cookie, $key);
	}

	/**
	 * Check if the given item exists
	 *
	 * @param string $key Item key
	 * @return boolean Does the item exist?
	 */
	#[ReturnTypeWillChange]
	public function offsetExists($key) {
		return isset($this->cookies[$key]);
	}

	/**
	 * Get the value for the item
	 *
	 * @param string $key Item key
	 * @return string|null Item value (null if offsetExists is false)
	 */
	#[ReturnTypeWillChange]
	public function offsetGet($key) {
		if (!isset($this->cookies[$key])) {
			return null;
		}

		return $this->cookies[$key];
	}

	/**
	 * Set the given item
	 *
	 * @throws \WpOrg\Requests\Exception On attempting to use dictionary as list (`invalidset`)
	 *
	 * @param string $key Item name
	 * @param string $value Item value
	 */
	#[ReturnTypeWillChange]
	public function offsetSet($key, $value) {
		if ($key === null) {
			throw new Exception('Object is a dictionary, not a list', 'invalidset');
		}

		$this->cookies[$key] = $value;
	}

	/**
	 * Unset the given header
	 *
	 * @param string $key
	 */
	#[ReturnTypeWillChange]
	public function offsetUnset($key) {
		unset($this->cookies[$key]);
	}

	/**
	 * Get an iterator for the data
	 *
	 * @return \ArrayIterator
	 */
	#[ReturnTypeWillChange]
	public function getIterator() {
		return new ArrayIterator($this->cookies);
	}

	/**
	 * Register the cookie handler with the request's hooking system
	 *
	 * @param \WpOrg\Requests\Hooker $hooks Hooking system
	 */
	public function register(Hooker $hooks) {
		$hooks->register('requests.before_request', array($this, 'before_request'));
		$hooks->register('requests.before_redirect_check', array($this, 'before_redirect_check'));
	}

	/**
	 * Add Cookie header to a request if we have any
	 *
	 * As per RFC 6265, cookies are separated by '; '
	 *
	 * @param string $url
	 * @param array $headers
	 * @param array $data
	 * @param string $type
	 * @param array $options
	 */
	public function before_request($url, &$headers, &$data, &$type, &$options) {
		if (!$url instanceof Iri) {
			$url = new Iri($url);
		}

		if (!empty($this->cookies)) {
			$cookies = array();
			foreach ($this->cookies as $key => $cookie) {
				$cookie = $this->normalize_cookie($cookie, $key);

				// Skip expired cookies
				if ($cookie->is_expired()) {
					continue;
				}

				if ($cookie->domain_matches($url->host)) {
					$cookies[] = $cookie->format_for_header();
				}
			}

			$headers['Cookie'] = implode('; ', $cookies);
		}
	}

	/**
	 * Parse all cookies from a response and attach them to the response
	 *
	 * @var \WpOrg\Requests\Response $response
	 */
	public function before_redirect_check(Response $return) {
		$url = $return->url;
		if (!$url instanceof Iri) {
			$url = new Iri($url);
		}

		$cookies         = Cookie::parse_from_headers($return->headers, $url);
		$this->cookies   = array_merge($this->cookies, $cookies);
		$return->cookies = $this;
	}
}
