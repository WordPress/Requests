<?php
/**
 * Case-insensitive dictionary, suitable for HTTP headers
 *
 * @package Requests
 */

namespace WpOrg\Requests\Response;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;
use WpOrg\Requests\Utility\FilteredIterator;

/**
 * Case-insensitive dictionary, suitable for HTTP headers
 *
 * @package Requests
 */
class Headers extends CaseInsensitiveDictionary {
	/**
	 * Get the given header
	 *
	 * Unlike {@see \WpOrg\Requests\Response\Headers::getValues()}, this returns a string. If there are
	 * multiple values, it concatenates them with a comma as per RFC2616.
	 *
	 * Avoid using this where commas may be used unquoted in values, such as
	 * Set-Cookie headers.
	 *
	 * @param string $offset
	 * @return string|null Header value
	 */
	public function offsetGet($offset) {
		$offset = strtolower($offset);
		if (!isset($this->data[$offset])) {
			return null;
		}

		return $this->flatten($this->data[$offset]);
	}

	/**
	 * Set the given item
	 *
	 * @throws \WpOrg\Requests\Exception On attempting to use dictionary as list (`invalidset`)
	 *
	 * @param string $offset Item name
	 * @param string $value Item value
	 */
	public function offsetSet($offset, $value) {
		if ($offset === null) {
			throw new Exception('Object is a dictionary, not a list', 'invalidset');
		}

		$offset = strtolower($offset);

		if (!isset($this->data[$offset])) {
			$this->data[$offset] = array();
		}

		$this->data[$offset][] = $value;
	}

	/**
	 * Get all values for a given header
	 *
	 * @param string $offset
	 * @return array|null Header values
	 */
	public function getValues($offset) {
		$offset = strtolower($offset);
		if (!isset($this->data[$offset])) {
			return null;
		}

		return $this->data[$offset];
	}

	/**
	 * Flattens a value into a string
	 *
	 * Converts an array into a string by imploding values with a comma, as per
	 * RFC2616's rules for folding headers.
	 *
	 * @param string|array $value Value to flatten
	 * @return string Flattened value
	 */
	public function flatten($value) {
		if (is_array($value)) {
			$value = implode(',', $value);
		}

		return $value;
	}

	/**
	 * Get an iterator for the data
	 *
	 * Converts the internally stored values to a comma-separated string if there is more
	 * than one value for a key.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new FilteredIterator($this->data, array($this, 'flatten'));
	}
}
