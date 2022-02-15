<?php

namespace WpOrg\Requests\Tests;

use Closure;
use stdClass;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Tests\Fixtures\ArrayAccessibleObject;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;

/**
 * @coversDefaultClass \WpOrg\Requests\Hooks
 */
class HooksTest extends TestCase {

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
	 * Technical test to verify the functionality of the Hooks::register() method.
	 *
	 * @covers ::register
	 *
	 * @return void
	 */
	public function testRegister() {
		// Verify initial state or the hooks property.
		$this->assertSame(
			[],
			$this->getPropertyValue($this->hooks, 'hooks'),
			'Initial state of $hooks is not an empty array'
		);

		// Verify that the subkeys are created correctly when they don't exist yet.
		$this->hooks->register('hookname', [$this, 'dummyCallback1']);
		$this->assertSame(
			[
				'hookname' => [
					0 => [
						[$this, 'dummyCallback1'],
					],
				],
			],
			$this->getPropertyValue($this->hooks, 'hooks'),
			'Initial hook registration failed'
		);

		// Verify that the subkeys are re-used when they already exist.
		$this->hooks->register('hookname', [$this, 'dummyCallback2']);
		$this->assertSame(
			[
				'hookname' => [
					0 => [
						[$this, 'dummyCallback1'],
						[$this, 'dummyCallback2'],
					],
				],
			],
			$this->getPropertyValue($this->hooks, 'hooks'),
			'Registering a second callback on the same hook with the same priority failed'
		);

		/*
		 * Verify that new subkeys are created when needed.
		 * Also verifies that the input validation isn't too strict for the priority.
		 */
		$this->hooks->register('hookname', 'is_int', '10');
		$this->assertSame(
			[
				'hookname' => [
					0  => [
						[$this, 'dummyCallback1'],
						[$this, 'dummyCallback2'],
					],
					10 => [
						'is_int',
					],
				],
			],
			$this->getPropertyValue($this->hooks, 'hooks'),
			'Registering a callback on a different priority for an existing hook failed'
		);
	}

	/**
	 * Technical test to verify and safeguard Hooks::register() accepts closure callbacks.
	 *
	 * @covers ::register
	 *
	 * @return void
	 */
	public function testRegisterClosureCallback() {
		$this->hooks->register(
			'hookname',
			function($param) {
				return true;
			}
		);

		$hooks_prop = $this->getPropertyValue($this->hooks, 'hooks');

		$this->assertArrayHasKey('hookname', $hooks_prop, '$hooks property does not have key ["hookname"]');
		$this->assertArrayHasKey(0, $hooks_prop['hookname'], '$hooks property does not have key ["hookname"][0]');
		$this->assertArrayHasKey(0, $hooks_prop['hookname'][0], '$hooks property does not have key ["hookname"][0][0]');
		$this->assertInstanceof(Closure::class, $hooks_prop['hookname'][0][0], 'Closure callback is not registered correctly');
	}


	/**
	 * Verify that the return value of the dispatch method is false when no hooks are registered.
	 *
	 * @covers ::dispatch
	 *
	 * @return void
	 */
	public function testDispatchWithoutRegisteredHooks() {
		$this->assertFalse($this->hooks->dispatch('hookname'));
	}

	/**
	 * Verify that the return value of the dispatch method is false when no hooks are registered for the hook called.
	 *
	 * @covers ::dispatch
	 *
	 * @return void
	 */
	public function testDispatchWithoutRegisteredHooksOnDispatchedHook() {
		$this->hooks->register('hookname', [$this, 'dummyCallback1']);

		$this->assertFalse($this->hooks->dispatch('other.hookname'));
	}

	/**
	 * Technical test to verify that the hook callbacks are called.
	 *
	 * @covers ::dispatch
	 *
	 * @return void
	 */
	public function testDispatchWithSingleRegisteredHook() {
		$mock = $this->getMockBuilder(stdClass::class)
			->setMethods(['callback'])
			->getMock();

		$mock->expects($this->once())
			->method('callback');

		$this->hooks->register('hookname', [$mock, 'callback']);

		$this->assertTrue($this->hooks->dispatch('hookname'));
	}

	/**
	 * Technical test to verify that the hook callbacks are called on the correct hook and with the expected arguments.
	 *
	 * @covers ::dispatch
	 *
	 * @return void
	 */
	public function testDispatchWithMultipleRegisteredHooks() {
		$mock = $this->getMockBuilder(stdClass::class)
			->setMethods(['callback_a', 'callback_b', 'callback_c'])
			->getMock();

		$mock->expects($this->never())
			->method('callback_a');

		$mock->expects($this->exactly(2))
			->method('callback_b')
			->with(
				$this->identicalTo(10),
				$this->identicalTo('text')
			);

		$mock->expects($this->once())
			->method('callback_c')
			->with(
				$this->identicalTo(10),
				$this->identicalTo('text')
			);

		$this->hooks->register('hook_a', [$mock, 'callback_a']);
		$this->hooks->register('hook_b', [$mock, 'callback_b']);
		$this->hooks->register('hook_b', [$mock, 'callback_b'], 10);
		$this->hooks->register('hook_b', [$mock, 'callback_c']);

		$this->assertTrue($this->hooks->dispatch('hook_b', [10, 'text']));
	}

	/**
	 * Technical test to verify that the dispatch method handles parameters passed by reference correctly.
	 *
	 * @covers ::dispatch
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
	 * Technical test to verify that the dispatch method doesn't break on PHP 8.0 when passed an associative array.
	 *
	 * @covers ::dispatch
	 *
	 * @return void
	 */
	public function testDispatchDoesntBreakWithKeyedParametersArray() {
		$this->hooks->register('hookname', [$this, 'dummyCallback1']);

		$this->assertTrue($this->hooks->dispatch('hookname', ['paramA' => 10, 'paramB' => 'text']));
	}

	/**
	 * Verify that hooks are executed based on their priority order.
	 *
	 * Issue https://github.com/WordPress/Requests/issues/452
	 *
	 * @covers ::dispatch
	 *
	 * @return void
	 */
	public function testDispatchRespectsHookPriority() {
		// Register multiple callbacks for the same hook with a variation of priorities.
		$this->hooks->register(
			'hook_a',
			function(&$text) {
				$text .= "no prio 0\n";
			}
		);
		$this->hooks->register(
			'hook_a',
			function(&$text) {
				$text .= "prio 10-1\n";
			},
			10
		);
		$this->hooks->register(
			'hook_a',
			function(&$text) {
				$text .= "prio -3\n";
			},
			-3
		);
		$this->hooks->register(
			'hook_a',
			function(&$text) {
				$text .= "prio 5\n";
			},
			5
		);
		$this->hooks->register(
			'hook_a',
			function(&$text) {
				$text .= "prio 2-1\n";
			},
			2
		);
		$this->hooks->register(
			'hook_a',
			function(&$text) {
				$text .= "prio 2-2\n";
			},
			2
		);
		$this->hooks->register(
			'hook_a',
			function(&$text) {
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
	 * Tests receiving an exception when an invalid input type is passed to `register()` as `$hook`.
	 *
	 * @dataProvider dataInvalidHookname
	 *
	 * @covers ::register
	 *
	 * @param mixed $input Invalid hook name input.
	 *
	 * @return void
	 */
	public function testRegisterInvalidHookname($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($hook) must be of type string');

		$this->hooks->register($input, 'is_string');
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to `dispatch()` as `$hook`.
	 *
	 * @dataProvider dataInvalidHookname
	 *
	 * @covers ::dispatch
	 *
	 * @param mixed $input Invalid hook name input.
	 *
	 * @return void
	 */
	public function testDispatchInvalidHookname($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($hook) must be of type string');

		$this->hooks->dispatch($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidHookname() {
		return [
			'null'              => [null],
			'float'             => [1.1],
			'stringable object' => [new StringableObject('value')],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to `register()` as `$callback`.
	 *
	 * @dataProvider dataRegisterInvalidCallback
	 *
	 * @covers ::register
	 *
	 * @param mixed $input Invalid callback.
	 *
	 * @return void
	 */
	public function testRegisterInvalidCallback($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($callback) must be of type callable');

		$this->hooks->register('hookname', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataRegisterInvalidCallback() {
		return [
			'null'                  => [null],
			'non-existent function' => ['functionname'],
			'non-existent method'   => [[$this, 'dummyCallbackDoesNotExist']],
			'empty array'           => [[]],
			'plain object'          => [new stdClass(), 'method'],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to `register()` as `$priority`.
	 *
	 * @dataProvider dataRegisterInvalidPriority
	 *
	 * @covers ::register
	 *
	 * @param mixed $input Invalid priority.
	 *
	 * @return void
	 */
	public function testRegisterInvalidPriority($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #3 ($priority) must be of type int');

		$this->hooks->register('hookname', [$this, 'dummyCallback1'], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataRegisterInvalidPriority() {
		return [
			'null'             => [null],
			'float'            => [1.1],
			'string "123 abc"' => ['123 abc'],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to `dispatch()` as `$parameters`.
	 *
	 * @dataProvider dataDispatchInvalidParameters
	 *
	 * @covers ::dispatch
	 *
	 * @param mixed $input Invalid parameters array.
	 *
	 * @return void
	 */
	public function testDispatchInvalidParameters($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($parameters) must be of type array');

		$this->hooks->dispatch('hookname', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataDispatchInvalidParameters() {
		return [
			'null'                            => [null],
			'bool false'                      => [false],
			'float'                           => [1.1],
			'string'                          => ['param'],
			'object implementing ArrayAccess' => [new ArrayAccessibleObject()],
		];
	}

	/**
	 * Dummy callback method.
	 *
	 * @return void
	 */
	public function dummyCallback1() {}

	/**
	 * Dummy callback method.
	 *
	 * @return void
	 */
	public function dummyCallback2() {}

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
