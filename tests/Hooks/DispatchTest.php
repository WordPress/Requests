<?php

namespace WpOrg\Requests\Tests\Hooks;

use ArrayObject;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Tests\Fixtures\CallbackRecorder;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Hooks::dispatch
 */
class DispatchTest extends TestCase {

	/**
	 * Object under test.
	 *
	 * @var \WpOrg\Requests\Hooks
	 */
	public $hooks;

	/**
	 * Initialize object under test.
	 *
	 * @return void
	 */
	protected function set_up() {
		$this->hooks = new Hooks();
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to `dispatch()` as `$hook`.
	 *
	 * @dataProvider dataInvalidHookname
	 *
	 * @param mixed $input Invalid hook name input.
	 *
	 * @return void
	 */
	public function testInvalidHookname($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($hook) must be of type string');

		$this->hooks->dispatch($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidHookname() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to `dispatch()` as `$parameters`.
	 *
	 * @dataProvider dataInvalidParameters
	 *
	 * @param mixed $input Invalid parameters array.
	 *
	 * @return void
	 */
	public function testInvalidParameters($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($parameters) must be of type array');

		$this->hooks->dispatch('hookname', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidParameters() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Verify that the return value of the dispatch method is false when no hooks are registered.
	 *
	 * @return void
	 */
	public function testDispatchWithoutRegisteredHooks() {
		$this->assertFalse($this->hooks->dispatch('hookname'));
	}

	/**
	 * Verify that the return value of the dispatch method is false when no hooks are registered for the hook called.
	 *
	 * @return void
	 */
	public function testDispatchWithoutRegisteredHooksOnDispatchedHook() {
		$this->hooks->register('hookname', [$this, 'dummyCallback']);

		$this->assertFalse($this->hooks->dispatch('other.hookname'));
	}

	/**
	 * Technical test to verify that the dispatch method doesn't break on PHP 8.0 when passed an associative array.
	 *
	 * @return void
	 */
	public function testDispatchDoesntBreakWithKeyedParametersArray() {
		$this->hooks->register('hookname', [$this, 'dummyCallback']);

		$this->assertTrue($this->hooks->dispatch('hookname', ['paramA' => 10, 'paramB' => 'text']));
	}

	/**
	 * Verify that hooks are executed based on their priority order.
	 *
	 * Issue https://github.com/WordPress/Requests/issues/452
	 *
	 * @return void
	 */
	public function testDispatchRespectsHookPriority() {
		// Register multiple callbacks for the same hook with a variation of priorities.
		$this->hooks->register(
			'hook_a',
			static function (&$text) {
				$text .= "no prio 0\n";
			}
		);
		$this->hooks->register(
			'hook_a',
			static function (&$text) {
				$text .= "prio 10-1\n";
			},
			10
		);
		$this->hooks->register(
			'hook_a',
			static function (&$text) {
				$text .= "prio -3\n";
			},
			-3
		);
		$this->hooks->register(
			'hook_a',
			static function (&$text) {
				$text .= "prio 5\n";
			},
			5
		);
		$this->hooks->register(
			'hook_a',
			static function (&$text) {
				$text .= "prio 2-1\n";
			},
			2
		);
		$this->hooks->register(
			'hook_a',
			static function (&$text) {
				$text .= "prio 2-2\n";
			},
			2
		);
		$this->hooks->register(
			'hook_a',
			static function (&$text) {
				$text .= "prio 10-2\n";
			},
			10
		);

		$text     = '';
		$expected = "prio -3\nno prio 0\nprio 2-1\nprio 2-2\nprio 5\nprio 10-1\nprio 10-2\n";

		$this->assertTrue($this->hooks->dispatch('hook_a', [&$text]));
		$this->assertSame($expected, $text);
	}

	/**
	 * Technical test to verify that the hook callbacks are called.
	 *
	 * @return void
	 */
	public function testDispatchWithSingleRegisteredHook() {
		$callback = new CallbackRecorder();

		$this->hooks->register('hookname', $callback);

		$this->assertTrue($this->hooks->dispatch('hookname'));
		$this->assertSame([[]], $callback->get_calls());
	}

	/**
	 * Technical test to verify that the hook callbacks are called on the correct hook and with the expected arguments.
	 *
	 * @return void
	 */
	public function testDispatchWithMultipleRegisteredHooks() {
		$call_order = new ArrayObject();
		$callback_a = new CallbackRecorder('a', $call_order);
		$callback_b = new CallbackRecorder('b', $call_order);
		$callback_c = new CallbackRecorder('c', $call_order);

		$this->hooks->register('hook_a', $callback_a);
		$this->hooks->register('hook_b', $callback_b);
		$this->hooks->register('hook_b', $callback_b, 10);
		$this->hooks->register('hook_b', $callback_c);

		$this->assertTrue($this->hooks->dispatch('hook_b', [10, 'text']));

		$this->assertSame([], $callback_a->get_calls(), 'Callback registered on another hook was called');
		$this->assertSame([[10, 'text'], [10, 'text']], $callback_b->get_calls(), 'Callback registered twice was not called twice with the expected arguments');
		$this->assertSame([[10, 'text']], $callback_c->get_calls(), 'Callback was not called once with the expected arguments');
		$this->assertSame(['b', 'c', 'b'], $call_order->getArrayCopy(), 'Callbacks were not called in priority order');
	}

	/**
	 * Technical test to verify that the dispatch method handles parameters passed by reference correctly.
	 *
	 * @return void
	 */
	public function testDispatchHandlesParametersPassedByReference() {
		$this->hooks->register('hookname', [$this, 'dummyCallbackExpectingReferences']);
		$url             = 'original';
		$headers         = ['original'];
		$unchanged       = 'original';
		$not_a_reference = 'original';

		$this->assertTrue(
			$this->hooks->dispatch('hookname', [&$url, &$headers, &$unchanged, $not_a_reference]),
			'Hook dispatch did not return true'
		);

		// Verify that the variables were updated by reference (when passed as reference).
		$this->assertSame('changed', $url, 'Value of $url was not changed by reference');
		$this->assertSame(['changed'], $headers, 'Value of $headers was not changed by reference');
		$this->assertSame('original', $unchanged, 'Value of unchanged parameter passed by reference, did not match original value');
		$this->assertSame('original', $not_a_reference, 'Value of parameter not passed by reference, did not match original value');
	}

	/**
	 * Dummy callback method.
	 *
	 * @return void
	 */
	public function dummyCallback() {}

	/**
	 * Dummy callback method.
	 *
	 * @return void
	 */
	public function dummyCallbackExpectingReferences(&$url, &$headers, &$unchanged, $not_a_reference) {
		$url             = 'changed';
		$headers         = ['changed'];
		$not_a_reference = 'changed';
	}
}
