<?php

namespace WpOrg\Requests\Tests\Fixtures;

/**
 * Invokable callback which records every call made to it.
 *
 * Used to verify that hook callbacks are called, how often and with which arguments,
 * without relying on the PHPUnit mock builder API, which is being phased out.
 * {@link https://github.com/WordPress/Requests/issues/814}
 */
final class CallbackRecorder {

	/**
	 * The arguments received for each call, in call order.
	 *
	 * @var array<array>
	 */
	private $calls = [];

	/**
	 * Record a call and the arguments it was called with.
	 *
	 * @return void
	 */
	public function __invoke() {
		$this->calls[] = func_get_args();
	}

	/**
	 * Retrieve the arguments received for each call, in call order.
	 *
	 * @return array<array>
	 */
	public function get_calls() {
		return $this->calls;
	}
}
