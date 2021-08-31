<?php

namespace WpOrg\Requests\Tests\Fixtures;

use ArrayAccess;
use ReturnTypeWillChange;

final class ArrayAccessibleObject implements ArrayAccess {
	private $value;

	public function __construct($value = array()) {
		$this->value = $value;
	}

	#[ReturnTypeWillChange]
	public function offsetExists($offset) {}

	#[ReturnTypeWillChange]
	public function offsetGet($offset) {}

	#[ReturnTypeWillChange]
	public function offsetSet($offset, $value) {}

	#[ReturnTypeWillChange]
	public function offsetUnset($offset) {}
}
