<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * @covers \WpOrg\Requests\Cookie::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidName($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($name) must be of type string');

		new Cookie($input, 'value');
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$value`.
	 *
	 * @dataProvider dataInvalidStringInput
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidValue($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($value) must be of type string');

		new Cookie('name', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidStringInput() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$name`.
	 *
	 * @dataProvider dataInvalidAttributes
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidAttributes($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #3 ($attributes) must be of type array|ArrayAccess&Traversable');

		new Cookie('name', 'value', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidAttributes() {
		$except = array_intersect(TypeProviderHelper::GROUP_ITERABLE, TypeProviderHelper::GROUP_ARRAY_ACCESSIBLE);
		return TypeProviderHelper::getAllExcept($except);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$flags`.
	 *
	 * @dataProvider dataInvalidFlags
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidFlags($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #4 ($flags) must be of type array');

		new Cookie('name', 'value', [], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidFlags() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$reference_time`.
	 *
	 * @dataProvider dataInvalidReferenceTime
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidReferenceTime($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #5 ($reference_time) must be of type integer|null');

		new Cookie('name', 'value', [], [], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidReferenceTime() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_NULL, TypeProviderHelper::GROUP_INT);
	}

	/**
	 * Tests the constructor handles valid data correctly when called with only the required arguments.
	 *
	 * @dataProvider dataMinimalArguments
	 *
	 * @param string $name  The name of the cookie.
	 * @param string $value The value for the cookie.
	 *
	 * @return void
	 */
	public function testMinimalArguments($name, $value) {
		$cookie = new Cookie($name, $value);

		$this->assertSame($name, $cookie->name, 'Name was not set correctly');
		$this->assertSame($value, $cookie->value, 'Value was not set correctly');
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataMinimalArguments() {
		return [
			'empty name and value' => [
				'name'  => '',
				'value' => '',
			],
			'non-empty name and value' => [
				'name'  => 'cookie-name',
				'value' => 'cookie-value',
			],
		];
	}

	/**
	 * Tests the constructor handles merging of flags correctly.
	 *
	 * @dataProvider dataFlagMerging
	 *
	 * @param array $flags    Passed flags.
	 * @param array $expected Flags as expected to be set by the function.
	 *
	 * @return void
	 */
	public function testFlagMerging($flags, $expected) {
		/*
		 * Set creation and last-access values last moment (if not set in the test case)
		 * to prevent the test from failing on time difference between when the data provider
		 * is called and when the actual test is run.
		 */
		if (!isset($expected['last-access'])) {
			$expected = $this->arrayUnshiftAssoc($expected, 'last-access', time());
		}

		if (!isset($expected['creation'])) {
			$expected = $this->arrayUnshiftAssoc($expected, 'creation', time());
		}

		$cookie = new Cookie('name', 'value', [], $flags);

		$this->assertSame($expected, $cookie->flags);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataFlagMerging() {
		return [
			'empty array' => [
				'flags'    => [],
				'expected' => [
					'persistent' => false,
					'host-only'  => true,
				],
			],
			'all supported flags passed' => [
				'flags'    => [
					'creation'    => 543547575,
					'last-access' => 543548555,
					'persistent'  => true,
					'host-only'   => false,
				],
				'expected' => [
					'creation'    => 543547575,
					'last-access' => 543548555,
					'persistent'  => true,
					'host-only'   => false,
				],
			],
			'unsupported keys are allowed through' => [
				'flags'    => [
					'creation'    => 12345,
					'last-access' => 12345,
					'invalid'     => 'invalid',
				],
				'expected' => [
					'creation'    => 12345,
					'last-access' => 12345,
					'persistent'  => false,
					'host-only'   => true,
					'invalid'     => 'invalid',
				],
			],
		];
	}

	/**
	 * Tests the constructor handles valid reference time setting correctly.
	 *
	 * @dataProvider dataSetReferenceTime
	 *
	 * @param mixed    $time     The passed reference time.
	 * @param int|null $expected Reference time as expected to be set by the function.
	 *
	 * @return void
	 */
	public function testSetReferenceTime($time, $expected = null) {
		/*
		 * Set expected time last moment (if not set in the test case) to prevent the test
		 * from failing on time difference between when the data provider
		 * is called and when the actual test is run.
		 */
		if (!isset($expected)) {
			$expected = time();
		}

		$cookie = new Cookie('name', 'value', [], [], $time);

		$this->assertSame($expected, $cookie->reference_time);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataSetReferenceTime() {
		return [
			'null' => [
				'time' => null,
			],
			'valid time' => [
				'time'     => 2178381267,
				'expected' => 2178381267,
			],
		];
	}

	/**
	 * Tests the constructor normalizes passed attributes.
	 *
	 * @dataProvider dataAttributesAreNormalized
	 *
	 * @param array $attributes The value to use for the attributes argument.
	 * @param array $expected   The attributes as they are expected to be set.
	 *
	 * @return void
	 */
	public function testAttributesAreNormalized($attributes, $expected) {
		$cookie = new Cookie('name', 'value', $attributes);

		$this->assertSame($expected, $cookie->attributes);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataAttributesAreNormalized() {
		return [
			'empty array' => [
				'attributes' => [],
				'expected'   => [],
			],
			'attributes array' => [
				'attributes' => [
					'domain'  => '',
					'expires' => '2022-04-07',
					'max-age' => 2874934,
				],
				'expected'   => [
					'expires' => 1649289600,
					'max-age' => 2874934,
				],
			],
		];
	}

	/**
	 * Tests the constructor normalizes passed attributes when the attributes are passed as a Dictionary object.
	 *
	 * @return void
	 */
	public function testAttributesAreNormalizedInDictionary() {
		$attributes = new CaseInsensitiveDictionary(
			[
				'domain'  => '',
				'expires' => '2022-04-07',
				'max-age' => 2874934,
			]
		);

		$expected = [
			'expires' => 1649289600,
			'max-age' => 2874934,
		];

		$cookie = new Cookie('name', 'value', $attributes);

		$this->assertSame($expected, $cookie->attributes->getAll());
	}


	/**
	 * Test helper function to prepend values onto an array and prevent issues with array order in assertions.
	 *
	 * @param array      $base_array The array to adjust.
	 * @param string|int $key        Key for the array item to add.
	 * @param mixed      $value      Value for the array item to add.
	 *
	 * @return array
	 */
	private function arrayUnshiftAssoc($base_array, $key, $value ) {
		$base_array       = array_reverse($base_array, true);
		$base_array[$key] = $value;
		return array_reverse($base_array, true);
	}
}
