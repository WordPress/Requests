<?php
namespace Rmccue\Requests\Response;

use Rmccue\Requests as Requests;
use Rmccue\Requests\Exception as Exception;
use Rmccue\Requests\Utility\FilteredIterator as FilteredIterator;
use Rmccue\Requests\Utility\CaseInsensitiveDictionary as CaseInsensitiveDictionary;
/**
 * Case-insensitive dictionary, suitable for HTTP headers
 *
 * @package Rmccue\Requests
 */

/**
 * Case-insensitive dictionary, suitable for HTTP headers
 *
 * @package Rmccue\Requests
 */
class Headers extends CaseInsensitiveDictionary {
	/**
	 * Get the given header
	 *
	 * Unlike {@see self::getValues()}, this returns a string. If there are
	 * multiple values, it concatenates them with a comma as per RFC2616.
	 *
	 * Avoid using this where commas may be used unquoted in values, such as
	 * Set-Cookie headers.
	 *
	 * @param string $key
	 * @return string Header value
	 */
	public function offsetGet($key) {
		$key = strtolower($key);
		if (!isset($this->data[$key])) {
			return null;
		}

		return $this->flatten($this->data[$key]);
	}

	/**
	 * Set the given item
	 *
	 * @throws Rmccue\Requests\Exception On attempting to use dictionary as list (`invalidset`)
	 *
	 * @param string $key Item name
	 * @param string $value Item value
	 */
	public function offsetSet($key, $value) {
		if ($key === null) {
			throw new Exception('Object is a dictionary, not a list', 'invalidset');
		}

		$key = strtolower($key);

		if (!isset($this->data[$key])) {
			$this->data[$key] = array();
		}

		$this->data[$key][] = $value;
	}

	/**
	 * Get all values for a given header
	 *
	 * @param string $key
	 * @return array Header values
	 */
	public function getValues($key) {
		$key = strtolower($key);
		if (!isset($this->data[$key])) {
			return null;
		}

		return $this->data[$key];
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
	 * Converts the internal
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new FilteredIterator($this->data, array($this, 'flatten'));
	}
}
