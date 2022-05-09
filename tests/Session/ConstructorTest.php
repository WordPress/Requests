<?php

namespace WpOrg\Requests\Tests\Session;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Session;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Session::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$url`.
	 *
	 * @dataProvider dataInvalidUrl
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidUrl($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($url) must be of type string|Stringable|null');

		new Session($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidUrl() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_NULL, TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$headers`.
	 *
	 * @dataProvider dataInvalidTypeNotArray
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidHeaders($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #2 ($headers) must be of type array');

		new Session(null, $input);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$data`.
	 *
	 * @dataProvider dataInvalidTypeNotArray
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidData($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #3 ($data) must be of type array');

		new Session('/', [], $input);
	}

	/**
	 * Tests receiving an exception when the constructor received an invalid input type as `$options`.
	 *
	 * @dataProvider dataInvalidTypeNotArray
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testInvalidOptions($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #4 ($options) must be of type array');

		new Session('/', [], [], $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotArray() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
	}

	/**
	 * Tests that valid input for the $url parameter for a new Session object is handled correctly.
	 *
	 * @dataProvider dataValidUrl
	 *
	 * @param mixed $input Valid parameter input.
	 *
	 * @return void
	 */
	public function testValidUrl($input) {
		$this->assertInstanceOf(Session::class, new Session($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataValidUrl() {
		return [
			'null'              => [null],
			'string'            => [httpbin('/')],
			'stringable object' => [new Iri(httpbin('/'))],
		];
	}
}
