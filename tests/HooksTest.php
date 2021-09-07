<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Hooks;
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

		// Verify that new subkeys are created when needed.
		$this->hooks->register('hookname', 'is_int', 10);
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
