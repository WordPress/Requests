<?php

namespace WpOrg\Requests\Tests\Fixtures;

final class StringableObject {
	private $value;

	public function __construct($value) {
		$this->value = $value;
	}

	public function __toString() {
		return (string) $this->value;
	}
}
