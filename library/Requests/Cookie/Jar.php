<?php
namespace Rmccue\Requests\Cookie;

Use Rmccue\Requests as Requests;
Use Rmccue\Requests\IRI as IRI;
Use Rmccue\Requests\Cookie as Cookie;
Use Rmccue\Requests\Exception as Exception;
Use Rmccue\Requests\Hooker as Hooker;
Use Rmccue\Requests\Response as Response;
/**
 * Cookie holder object
 *
 * @package Rmccue\Requests
 * @subpackage Cookies
 */

/**
 * Cookie holder object
 *
 * @package Rmccue\Requests
 * @subpackage Cookies
 */
class Jar implements \ArrayAccess, \IteratorAggregate {
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
	 * Normalise cookie data into a Rmccue\Requests\Cookie
	 *
	 * @param string|Rmccue\Requests\Cookie $cookie
	 * @return \Rmccue\Requests\Cookie
	 */
	public function normalize_cookie($cookie, $key = null) {
		if ($cookie instanceof Cookie) {
			return $cookie;
		}

		return Cookie::parse($cookie, $key);
	}

	/**
	 * Normalise cookie data into a Rmccue\Requests\Cookie
	 *
	 * @codeCoverageIgnore
	 * @deprecated Use {@see Rmccue\Requests\Cookie\Jar::normalize_cookie}
	 * @return Rmccue\Requests\Cookie
	 */
	public function normalizeCookie($cookie, $key = null) {
		return $this->normalize_cookie($cookie, $key);
	}

	/**
	 * Check if the given item exists
	 *
	 * @param string $key Item key
	 * @return boolean Does the item exist?
	 */
	public function offsetExists($key) {
		return isset($this->cookies[$key]);
	}

	/**
	 * Get the value for the item
	 *
	 * @param string $key Item key
	 * @return string Item value
	 */
	public function offsetGet($key) {
		if (!isset($this->cookies[$key])) {
			return null;
		}

		return $this->cookies[$key];
	}

	/**
	 * Set the given item
	 *
	 * @throws \Rmccue\Requests\Exception On attempting to use dictionary as list (`invalidset`)
	 *
	 * @param string $key Item name
	 * @param string $value Item value
	 */
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
	public function offsetUnset($key) {
		unset($this->cookies[$key]);
	}

	/**
	 * Get an iterator for the data
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator($this->cookies);
	}

	/**
	 * Register the cookie handler with the request's hooking system
	 *
	 * @param Rmccue\Requests\Hooker $hooks Hooking system
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
		if (!$url instanceof IRI) {
			$url = new IRI($url);
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
	 * @var Rmccue\Requests\Response $response
	 */
	public function before_redirect_check(Response &$return) {
		$url = $return->url;
		if (!$url instanceof IRI) {
			$url = new IRI($url);
		}

		$cookies = Cookie::parse_from_headers($return->headers, $url);
		$this->cookies = array_merge($this->cookies, $cookies);
		$return->cookies = $this;
	}
}