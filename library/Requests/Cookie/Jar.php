<?php
/**
 * Cookie holder object
 *
 * @package Requests
 * @subpackage Cookies
 */

/**
 * Cookie holder object
 *
 * @package Requests
 * @subpackage Cookies
 */
class Requests_Cookie_Jar implements ArrayAccess, IteratorAggregate {
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
	 * Normalise cookie data into a Requests_Cookie
	 *
	 * @param string|Requests_Cookie $cookie
	 * @return Requests_Cookie
	 */
	public function normalizeCookie($cookie) {
		if (is_a('Requests_Cookie', $cookie)) {
			return $cookie;
		}

		return Requests_Cookie::parse($cookie);
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
		if (!isset($this->cookies[$key]))
			return null;

		return $this->cookies[$key];
	}

	/**
	 * Set the given item
	 *
	 * @throws Requests_Exception On attempting to use dictionary as list (`invalidset`)
	 *
	 * @param string $key Item name
	 * @param string $value Item value
	 */
	public function offsetSet($key, $value) {
		if ($key === null) {
			throw new Requests_Exception('Object is a dictionary, not a list', 'invalidset');
		}

		$this->cookies[$key][] = $value;
	}

	/**
	 * Unset the given header
	 *
	 * @param string $key
	 */
	public function offsetUnset($key) {
		unset($this->data[$key]);
	}

	/**
	 * Get an iterator for the data
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->data);
	}

	/**
	 * Register the cookie handler with the request's hooking system
	 *
	 * @param Requests_Hooker $hooks Hooking system
	 */
	public function register(Requests_Hooker $hooks) {
		$hooks->register('requests.before_request', array($this, 'before_request'));
		$hooks->register('requests.after_request', array($this, 'after_request'));
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
	public function before_request(&$url, &$headers, &$data, &$type, &$options) {
		if (!empty($this->cookies)) {
			$cookies = array();
			foreach ($this->cookies as $cookie) {
				$cookies[] = $cookie->formatForHeader();
			}

			$headers['Cookie'] = implode('; ', $cookies);
		}
	}

	/**
	 * Parse all cookies from a response and attach them to the response
	 *
	 * @var Requests_Response $response
	 */
	public function after_request(Requests_Response &$return) {
		$cookies = Requests_Cookie::parseFromHeaders($return->headers);
		$return->cookies = new Requests_Cookie_Jar($cookies);
	}
}