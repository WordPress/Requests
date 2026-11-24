<?php

namespace WpOrg\Requests\Tests\Fixtures;

use ArrayObject;

/**
 * Invokable callback which records every call made to it.
 *
 * Used to verify that hook callbacks are called, how often and with which arguments,
 * without relying on the PHPUnit mock builder API, which is being phased out.
 * {@link https://github.com/WordPress/Requests/issues/814}
 *
 * To verify the order in which several callbacks are called, give each recorder a name
 * and pass the same call order log to all of them.
 */
final class CallbackRecorder {

	/**
	 * The arguments received for each call, in call order.
	 *
	 * @var array<array>
	 */
	private $calls = [];

	/**
	 * Name to add to the call order log for each call.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Call order log, which can be shared between recorders.
	 *
	 * @var \ArrayObject|null
	 */
	private $call_order;

	/**
	 * Constructor.
	 *
	 * @param string            $name       Optional. Name to add to the call order log for each call.
	 * @param \ArrayObject|null $call_order Optional. Call order log to add the name to.
	 */
	public function __construct($name = '', $call_order = null) {
		$this->name       = $name;
		$this->call_order = $call_order instanceof ArrayObject ? $call_order : new ArrayObject();
	}

	/**
	 * Record a call and the arguments it was called with.
	 *
	 * @return void
	 */
	public function __invoke() {
		$this->calls[]      = func_get_args();
		$this->call_order[] = $this->name;
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
