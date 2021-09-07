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
	 * @var \Requests\Hooks
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
			array(),
			$this->getPropertyValue($this->hooks, 'hooks'),
			'Initial state of $hooks is not an empty array'
		);

		// Verify that the subkeys are created correctly when they don't exist yet.
		$this->hooks->register('hookname', array($this, 'dummyCallback1'));
		$this->assertSame(
			array(
				'hookname' => array(
					0 => array(
						array($this, 'dummyCallback1'),
					),
				),
			),
			$this->getPropertyValue($this->hooks, 'hooks'),
			'Initial hook registration failed'
		);

		// Verify that the subkeys are re-used when they already exist.
		$this->hooks->register('hookname', array($this, 'dummyCallback2'));
		$this->assertSame(
			array(
				'hookname' => array(
					0 => array(
						array($this, 'dummyCallback1'),
						array($this, 'dummyCallback2'),
					),
				),
			),
			$this->getPropertyValue($this->hooks, 'hooks'),
			'Registering a second callback on the same hook with the same priority failed'
		);

		/*
		 * Verify that new subkeys are created when needed.
		 * Also verifies that the input validation isn't too strict for the priority.
		 */
		$this->hooks->register('hookname', 'is_int', '10');
		$this->assertSame(
			array(
				'hookname' => array(
					0  => array(
						array($this, 'dummyCallback1'),
						array($this, 'dummyCallback2'),
					),
					10 => array(
						'is_int',
					),
				),
			),
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
		$this->hooks->register('hookname', array($this, 'dummyCallback1'));

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
			->setMethods(array('callback'))
			->getMock();

		$mock->expects($this->once())
			->method('callback');

		$this->hooks->register('hookname', array($mock, 'callback'));

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
			->setMethods(array('callback_a', 'callback_b', 'callback_c'))
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

		$this->hooks->register('hook_a', array($mock, 'callback_a'));
		$this->hooks->register('hook_b', array($mock, 'callback_b'));
		$this->hooks->register('hook_b', array($mock, 'callback_b'), 10);
		$this->hooks->register('hook_b', array($mock, 'callback_c'));

		$this->assertTrue($this->hooks->dispatch('hook_b', array(10, 'text')));
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
		return array(
			'null'              => array(null),
			'float'             => array(1.1),
			'stringable object' => array(new StringableObject('value')),
		);
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
		return array(
			'null'                  => array(null),
			'non-existent function' => array('functionname'),
			'non-existent method'   => array(array($this, 'dummyCallbackDoesNotExist')),
			'empty array'           => array(array()),
			'plain object'          => array(new stdClass(), 'method'),
		);
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

		$this->hooks->register('hookname', array($this, 'dummyCallback1'), $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataRegisterInvalidPriority() {
		return array(
			'null'             => array(null),
			'float'            => array(1.1),
			'string "123 abc"' => array('123 abc'),
		);
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
		return array(
			'null'                            => array(null),
			'bool false'                      => array(false),
			'float'                           => array(1.1),
			'string'                          => array('param'),
			'object implementing ArrayAccess' => array(new ArrayAccessibleObject()),
		);
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
}
