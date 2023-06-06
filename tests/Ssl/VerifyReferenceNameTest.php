<?php

namespace WpOrg\Requests\Tests\Ssl;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Ssl;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Ssl::verify_reference_name
 */
final class VerifyReferenceNameTest extends TestCase {

	/**
	 * Tests receiving an exception when an invalid input type is passed.
	 *
	 * @dataProvider dataInvalidInputType
	 *
	 * @param mixed $input Input data.
	 *
	 * @return void
	 */
	public function testInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($reference) must be of type string|Stringable');

		Ssl::verify_reference_name($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataInvalidInputType() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Test correctly identifying whether a reference name is valid.
	 *
	 * @dataProvider dataVerifyReferenceName
	 *
	 * @param string $reference Reference name to test.
	 * @param bool   $expected  Expected function outcome.
	 *
	 * @return void
	 */
	public function testVerifyReferenceName($reference, $expected) {
		$this->assertSame($expected, Ssl::verify_reference_name($reference));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataVerifyReferenceName() {
		return [
			'empty string' => [
				'reference' => '',
				'expected'  => false,
			],
			'one part, no dot' => [
				'reference' => 'example',
				'expected'  => true,
			],
			'one part, only wildcard' => [
				'reference' => '*',
				'expected'  => false,
			],
			'two parts, no wildcard' => [
				'reference' => 'example.com',
				'expected'  => true,
			],
			'two parts, wildcard in first' => [
				'reference' => '*.com',
				'expected'  => false,
			],
			'two parts, wildcard in last' => [
				'reference' => 'example.*',
				'expected'  => false,
			],
			'three parts, only dots' => [
				'reference' => '..',
				'expected'  => false,
			],
			'three parts, no wildcard' => [
				'reference' => new StringableObject('www.example.com'),
				'expected'  => true,
			],
			'three parts, no wildcard, has spaces' => [
				'reference' => 'my dog . and . my cat',
				'expected'  => false,
			],
			'three parts, wildcard in first' => [
				'reference' => '*.example.com',
				'expected'  => true,
			],
			'three parts, wildcard in second' => [
				'reference' => 'www.*.com',
				'expected'  => false,
			],
			'three parts, wildcard in third' => [
				'reference' => 'www.example.*',
				'expected'  => false,
			],
			'three parts, wildcard in first at start with other characters' => [
				'reference' => '*ww.example.com',
				'expected'  => false,
			],
			'three parts, wildcard in first at end with other characters' => [
				'reference' => 'ww*.example.com',
				'expected'  => false,
			],
			'three parts, wildcard in first and second' => [
				'reference' => '*.*.com',
				'expected'  => false,
			],
			'three parts, wildcard in second and last' => [
				'reference' => 'www.*.*',
				'expected'  => false,
			],
			'three parts, wildcard in first and last' => [
				'reference' => '*.example.*',
				'expected'  => false,
			],
		];
	}
}
