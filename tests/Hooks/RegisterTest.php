<?php

namespace WpOrg\Requests\Tests\Hooks;

use Closure;
use stdClass;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Hooks::register
 */
class RegisterTest extends TestCase {

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
	 * Tests receiving an exception when an invalid input type is passed to `register()` as `$hook`.
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

		$this->hooks->register($input, 'is_string');
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
	 * Tests receiving an exception when an invalid input type is passed to `register()` as `$callback`.
	 *
	 * @dataProvider dataInvalidCallback
	 *
	 * @param mixed $input Invalid callback.
	 *
	 * @return void
	 */
	public function testInvalidCallback($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($callback) must be of type callable');

		$this->hooks->register('hookname', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidCallback() {
		return [
			'null'                  => [null],
			'non-existent function' => ['functionname'],
			'non-existent method'   => [[__CLASS__, 'dummyCallbackDoesNotExist']],
			'empty array'           => [[]],
			'plain object'          => [new stdClass(), 'method'],
		];
	}

	/**
	 * Tests receiving an exception when an invalid input type is passed to `register()` as `$priority`.
	 *
	 * @dataProvider dataInvalidPriority
	 *
	 * @param mixed $input Invalid priority.
	 *
	 * @return void
	 */
	public function testInvalidPriority($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #3 ($priority) must be of type int');

		$this->hooks->register('hookname', [$this, 'dummyCallback1'], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidPriority() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT, ['numeric string']);
	}

	/**
	 * Technical test to verify the functionality of the Hooks::register() method.
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
	 * @return void
	 */
	public function testRegisterClosureCallback() {
		$this->hooks->register(
			'hookname',
			static function() {
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
