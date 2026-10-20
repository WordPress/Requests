<?php

namespace WpOrg\Requests\Tests;

use ArrayIterator;
use EmptyIterator;
use stdClass;
use WpOrg\Requests\Tests\Fixtures\ArrayAccessibleObject;
use WpOrg\Requests\Tests\Fixtures\StringableObject;

/**
 * Helper class to provide an exhaustive list of types to test type safety.
 */
final class TypeProviderHelper {

	/**
	 * Keys of all type entries representing null.
	 *
	 * @var array<string>
	 */
	const GROUP_NULL = ['null'];

	/**
	 * Keys of all type entries representing a boolean.
	 *
	 * @var array<string>
	 */
	const GROUP_BOOL = [
		'boolean false',
		'boolean true',
	];

	/**
	 * Keys of all type entries representing an integer.
	 *
	 * @var array<string>
	 */
	const GROUP_INT = [
		'integer 0',
		'negative integer',
		'positive integer',
	];

	/**
	 * Keys of all type entries representing a float.
	 *
	 * @var array<string>
	 */
	const GROUP_FLOAT = [
		'float 0.0',
		'negative float',
		'positive float',
	];

	/**
	 * Keys of all type entries representing an integer or float.
	 *
	 * @var array<string>
	 */
	const GROUP_INT_FLOAT = [
		'integer 0',
		'negative integer',
		'positive integer',
		'float 0.0',
		'negative float',
		'positive float',
	];

	/**
	 * Keys of all type entries representing a string.
	 *
	 * @var array<string>
	 */
	const GROUP_STRING = [
		'empty string',
		'numeric string',
		'textual string',
		'textual string starting with numbers',
	];

	/**
	 * Keys of all type entries which are stringable.
	 *
	 * @var array<string>
	 */
	const GROUP_STRINGABLE = [
		'empty string',
		'numeric string',
		'textual string',
		'textual string starting with numbers',
		'Stringable object',
	];

	/**
	 * Keys of all type entries representing an array.
	 *
	 * @var array<string>
	 */
	const GROUP_ARRAY = [
		'empty array',
		'array with values, no keys',
		'array with values, string keys',
	];

	/**
	 * Keys of all type entries which are iterable.
	 *
	 * @var array<string>
	 */
	const GROUP_ITERABLE = [
		'empty array',
		'array with values, no keys',
		'array with values, string keys',
		'ArrayIterator object',
		'Iterator object, no array access',
	];

	/**
	 * Keys of all type entries which have array access.
	 *
	 * @var array<string>
	 */
	const GROUP_ARRAY_ACCESSIBLE = [
		'empty array',
		'array with values, no keys',
		'array with values, string keys',
		'ArrayIterator object',
		'ArrayAccess object',
	];

	/**
	 * Keys of all type entries representing an object.
	 *
	 * @var array<string>
	 */
	const GROUP_OBJECT = [
		'plain object',
		'Stringable object',
		'ArrayIterator object',
		'ArrayAccess object',
		'Iterator object, no array access',
	];

	/**
	 * Keys of all type entries representing a resource.
	 *
	 * @var array<string>
	 */
	const GROUP_RESOURCE = [
		'resource (open file handle)',
		'resource (closed file handle)',
	];

	/**
	 * Keys of all type entries which are considered empty.
	 *
	 * @var array<string>
	 */
	const GROUP_EMPTY = [
		'null',
		'boolean false',
		'integer 0',
		'float 0.0',
		'empty string',
		'empty array',
	];

	/**
	 * File handle to local memory (open resource).
	 *
	 * @var resource|null
	 */
	private static $memory_handle_open;

	/**
	 * File handle to local memory (closed resource).
	 *
	 * @var resource|null
	 */
	private static $memory_handle_closed;

	/**
	 * Clean up after the tests.
	 *
	 * This method should be called in the `tear_down_after_class()` of any test class
	 * using these helper functions.
	 *
	 * @return void
	 */
	public static function cleanUp() {
		if (isset(self::$memory_handle_open)) {
			fclose(self::$memory_handle_open);
			unset(self::$memory_handle_open);
		}
	}

	/**
	 * Retrieve an array in data provider format with a selection of all typical PHP data types
	 * *except* the named types specified in the $except parameter.
	 *
	 * @param array<string> ...$except One or more arrays containing the names of the types to exclude.
	 *                                 Typically, one or more of the predefined "groups" (see the constants)
	 *                                 would be used here.
	 *
	 * @return array<string, array<mixed>>
	 */
	public static function getAllExcept(array ...$except) {
		$except = array_flip(array_merge(...$except));

		return array_diff_key(self::getAll(), $except);
	}

	/**
	 * Retrieve an array in data provider format with a selection of typical PHP data types.
	 *
	 * @param array<string> ...$selection One or more arrays containing the names of the types to include.
	 *                                    Typically, one or more of the predefined "groups" (see the constants)
	 *                                    would be used here.
	 *
	 * @return array<string, array<mixed>>
	 */
	public static function getSelection(array ...$selection) {
		$selection = array_flip(array_merge(...$selection));

		return array_intersect_key(self::getAll(), $selection);
	}

	/**
	 * Retrieve an array in data provider format with all typical PHP data types.
	 *
	 * @return array<string, array<mixed>>
	 */
	public static function getAll() {
		if (isset(self::$memory_handle_open) === false) {
			self::$memory_handle_open = fopen('php://memory', 'r+');
		}

		if (isset(self::$memory_handle_closed) === false) {
			self::$memory_handle_closed = fopen('php://memory', 'r+');
			fclose(self::$memory_handle_closed);
		}

		return [
			'null'                                 => [null],
			'boolean false'                        => [false],
			'boolean true'                         => [true],
			'integer 0'                            => [0],
			'negative integer'                     => [-123],
			'positive integer'                     => [786687],
			'float 0.0'                            => [0.0],
			'negative float'                       => [5.600e-3],
			'positive float'                       => [124.7],
			'empty string'                         => [''],
			'numeric string'                       => ['123'],
			'textual string'                       => ['foobar'],
			'textual string starting with numbers' => ['123 My Street'],
			'empty array'                          => [[]],
			'array with values, no keys'           => [[1, 2, 3]],
			'array with values, string keys'       => [['a' => 1, 'b' => 2]],
			'plain object'                         => [new stdClass()],
			'Stringable object'                    => [new StringableObject('value')],
			'ArrayIterator object'                 => [new ArrayIterator([1, 2, 3])],
			'ArrayAccess object'                   => [new ArrayAccessibleObject()],
			'Iterator object, no array access'     => [new EmptyIterator()],
			'resource (open file handle)'          => [self::$memory_handle_open],
			'resource (closed file handle)'        => [self::$memory_handle_closed],
		];
	}
}
