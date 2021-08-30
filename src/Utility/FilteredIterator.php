<?php
/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests
 * @subpackage Utilities
 */

namespace WpOrg\Requests\Utility;

use ArrayIterator;
use ReturnTypeWillChange;

/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests
 * @subpackage Utilities
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
	 * @param array $data
	 * @param callable $callback Callback to be called on each value
	 */
	public function __construct($data, $callback) {
		parent::__construct($data);

		$this->callback = $callback;
	}

	/**
	 * @inheritdoc
	 *
	 * @phpcs:disable PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__unserializeFound
	 */
	#[ReturnTypeWillChange]
	public function __unserialize($serialized) {}
	// phpcs:enable

	public function __wakeup() {
		unset($this->callback);
	}

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

	/**
	 * @inheritdoc
	 */
	#[ReturnTypeWillChange]
	public function unserialize($serialized) {}
}
