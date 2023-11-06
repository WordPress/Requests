<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Utility;

use ArrayIterator;
use ReturnTypeWillChange;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Utility\InputValidator;

/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests\Utilities
 */
final class FilteredIterator extends ArrayIterator {
	/**
	 * Callback to run as a filter
	 *
	 * @var callable
	 */
	private $callback;

	/**
	 * Create a new iterator
	 *
	 * @param array    $data     The array or object to be iterated on.
	 * @param callable $callback Callback to be called on each value
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $data argument is not iterable.
	 */
	public function __construct($data, $callback) {
		if (InputValidator::is_iterable($data) === false) {
			throw InvalidArgument::create(1, '$data', 'iterable', gettype($data));
		}

		parent::__construct($data);

		if (is_callable($callback)) {
			$this->callback = $callback;
		}
	}

	/**
	 * Prevent unserialization of the object for security reasons.
	 *
	 * This method is used on PHP 7.4+.
	 *
	 * @phpcs:disable PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__unserializeFound
	 *
	 * @param array $data Restored array of data originally serialized.
	 *
	 * @return void
	 */
	#[ReturnTypeWillChange]
	public function __unserialize($data) {}
	// phpcs:enable

	/**
	 * Prevent creating a PHP value from a stored representation of the object for security reasons.
	 *
	 * This method is used on PHP < 7.4.
	 *
	 * @param string $data The serialized string.
	 *
	 * @return void
	 */
	#[ReturnTypeWillChange]
	public function unserialize($data) {}

	/**
	 * Get the current item's value after filtering
	 *
	 * @return string
	 */
	#[ReturnTypeWillChange]
	public function current() {
		$value = parent::current();

		if (is_callable($this->callback)) {
			$value = call_user_func($this->callback, $value);
		}

		return $value;
	}
}
