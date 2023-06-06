<?php

namespace WpOrg\Requests\Tests\Cookie;

use WpOrg\Requests\Cookie;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Cookie::normalize
 * @covers \WpOrg\Requests\Cookie::normalize_attribute
 */
final class NormalizeTest extends TestCase {

	/**
	 * Verify cookie attribute normalization works correctly.
	 *
	 * @dataProvider dataNormalizeAttributes
	 * @dataProvider dataNormalizeAttributesExpiresUnsupportedType
	 * @dataProvider dataNormalizeAttributesMaxAgeUnsupportedType
	 * @dataProvider dataNormalizeAttributesDomainUnsupportedType
	 *
	 * @param array $attributes The attributes used for creating the cookie.
	 * @param array $expected   The expected attributes after normalization.
	 *
	 * @return void
	 */
	public function testNormalizeAttributes($attributes, $expected) {
		$cookie = new Cookie('requests-testcookie', 'testvalue', $attributes);

		$this->assertSame($expected, $cookie->attributes);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataNormalizeAttributes() {
		return [
			/*
			 * Test cases specific to the normalize() method.
			 */
			'Empty attributes array' => [
				'attributes' => [],
				'expected'   => [],
			],
			'Unnecessary/invalid attributes will be unset' => [
				'attributes' => [
					'domain'  => '',
					'expires' => false,
					'max-age' => 'invalid',
				],
				'expected'   => [],
			],
			'Attributes which do not change during normalization are not updated' => [
				'attributes' => [
					'domain'  => 'example.com',
					'expires' => 12345,
					'max-age' => 6789,
					'other'   => 'attribute which does not get normalized',
				],
				'expected'   => [
					'domain'  => 'example.com',
					'expires' => 12345,
					'max-age' => 6789,
					'other'   => 'attribute which does not get normalized',
				],
			],
			'Mix of attributes which will be unset, changed and not changed' => [
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

			/*
			 * Test cases specific to the normalize_attribute() method.
			 */
			'Attribute normalization: attribute names are handled case-insensitively and case is not changed on update' => [
				'attributes' => [
					'DOMAIN'  => 'example.com',
					'Expires' => '10 September 2000',
					'MAX-Age' => '-10',
				],
				'expected'   => [
					'DOMAIN'  => 'example.com',
					'Expires' => 968544000,
					'MAX-Age' => 0,
				],
			],

			// Expires logic.
			'Attribute normalization: expires: integer value is returned unchanged' => [
				'attributes' => [
					'expires' => 7483932,
				],
				'expected'   => [
					'expires' => 7483932,
				],
			],
			'Attribute normalization: expires: negative integer value is returned unchanged' => [
				'attributes' => [
					'expires' => -7483932,
				],
				'expected'   => [
					'expires' => -7483932,
				],
			],
			'Attribute normalization: expires: text string which doesn\'t resolve to a time stamp will be unset' => [
				'attributes' => [
					'expires' => 'not a date',
				],
				'expected'   => [],
			],
			'Attribute normalization: expires: text string which resolves to a time stamp will be updated' => [
				'attributes' => [
					'expires' => '2022-04-07',
				],
				'expected'   => [
					'expires' => 1649289600,
				],
			],
			'Attribute normalization: expires: text string which resolves to a time stamp will be updated (negative int)' => [
				'attributes' => [
					'expires' => '1963-04-07',
				],
				'expected'   => [
					'expires' => -212630400,
				],
			],

			// Max-age logic.
			'Attribute normalization: max-age: integer value is returned unchanged' => [
				'attributes' => [
					'max-age' => 2874934,
				],
				'expected'   => [
					'max-age' => 2874934,
				],
			],
			'Attribute normalization: max-age: negative integer value is returned unchanged' => [
				'attributes' => [
					'max-age' => -2874934,
				],
				'expected'   => [
					'max-age' => -2874934,
				],
			],
			'Attribute normalization: max-age: text string where first char is not a "-" or digit will be unset' => [
				'attributes' => [
					'max-age' => 'not an age',
				],
				'expected'   => [],
			],
			'Attribute normalization: max-age: non-numeric text string starting with a "-" but not followed by digit(s) will be unset' => [
				'attributes' => [
					'max-age' => '-non-digit',
				],
				'expected'   => [],
			],
			'Attribute normalization: max-age: non-numeric text string with digit(s) and text will be unset' => [
				'attributes' => [
					'max-age' => '273128361text',
				],
				'expected'   => [],
			],
			'Attribute normalization: max-age: negative numeric text string will be normalized to 0 (earliest representable date time)' => [
				'attributes' => [
					'max-age' => '-10',
				],
				'expected'   => [
					'max-age' => 0,
				],
			],
			'Attribute normalization: max-age: numeric text string zero ("0") will be normalized to 0 (earliest representable date time)' => [
				'attributes' => [
					'max-age' => '0',
				],
				'expected'   => [
					'max-age' => 0,
				],
			],
			// Attribute normalization of max-age with a positive numeric text string is tested separately.

			// Domain logic.
			'Attribute normalization: domain: empty domain (empty string) will be unset' => [
				'attributes' => [
					'domain' => '',
				],
				'expected'   => [],
			],
			'Attribute normalization: domain: empty domain (null) will be unset' => [
				'attributes' => [
					'domain' => null,
				],
				'expected'   => [],
			],
			'Attribute normalization: domain: domain with first char "." should have the first char stripped' => [
				'attributes' => [
					'domain' => '.example.com',
				],
				'expected'   => [
					'domain' => 'example.com',
				],
			],
			'Attribute normalization: domain: domain without a dot as first char is returned unchanged' => [
				'attributes' => [
					'domain' => 'example.com',
				],
				'expected'   => [
					'domain' => 'example.com',
				],
			],
			'Attribute normalization: domain: domain should be converted to lowercase - ascii' => [
				'attributes' => [
					'domain' => 'EXAMPLE.COM',
				],
				'expected'   => [
					'domain' => 'example.com',
				],
			],
			'Attribute normalization: domain: domain should be converted to lowercase - chinese' => [
				'attributes' => [
					'domain' => "\xe4\xbb\x96\xe4\xbb\xac\xe4\xb8\xba\xe4\xbb\x80\xe4\xb9\x88\xe4\xb8\x8d\xe8\xaf\xb4\xe4\xb8\xad\xe6\x96\x87.COM",
				],
				'expected'   => [
					'domain' => 'xn--ihqwcrb4cv8a8dqg056pqjye.com',
				],
			],
			'Attribute normalization: domain: domain should be converted to lowercase - accented latin' => [
				'attributes' => [
					'domain' => "\x50\x6f\x72\x71\x75\xc3\xa9\x6e\x6f\x70\x75\x65\x64\x65\x6e\x73\x69\x6d\x70\x6c\x65\x6d\x65\x6e\x74\x65\x68\x61\x62\x6c\x61\x72\x65\x6e\x45\x73\x70\x61\xc3\xb1\x6f\x6c.ES",
				],
				'expected'   => [
					'domain' => 'xn--porqunopuedensimplementehablarenespaol-fmd56a.es',
				],
			],

			// Default case logic.
			'Attribute normalization: anything else is returned unchanged - string name, string value' => [
				'attributes' => [
					'some-key' => 'some-value',
				],
				'expected'   => [
					'some-key' => 'some-value',
				],
			],
			'Attribute normalization: anything else is returned unchanged - string name, boolean value' => [
				'attributes' => [
					'some-key' => false,
				],
				'expected'   => [
					'some-key' => false,
				],
			],
			'Attribute normalization: anything else is returned unchanged - no name (automatic numeric index), float value' => [
				'attributes' => [
					213214.34234,
				],
				'expected'   => [
					213214.34234,
				],
			],
		];
	}

	/**
	 * Data provider for checking data type handling for the "expires" attribute.
	 *
	 * @return array
	 */
	public static function dataNormalizeAttributesExpiresUnsupportedType() {
		$types = TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT, TypeProviderHelper::GROUP_STRING);

		$data = [];
		foreach ($types as $key => $value) {
			$data['Attribute normalization: expires: unsupported type - ' . $key] = [
				'attributes' => [
					'expires' => $value['input'],
				],
				'expected'   => [],
			];
		}

		return $data;
	}

	/**
	 * Data provider for checking data type handling for the "max-age" attribute.
	 *
	 * @return array
	 */
	public static function dataNormalizeAttributesMaxAgeUnsupportedType() {
		$types = TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT, TypeProviderHelper::GROUP_STRING);

		$data = [];
		foreach ($types as $key => $value) {
			$data['Attribute normalization: max-age: unsupported type - ' . $key] = [
				'attributes' => [
					'max-age' => $value['input'],
				],
				'expected'   => [],
			];
		}

		return $data;
	}

	/**
	 * Data provider for checking data type handling for the "domain" attribute.
	 *
	 * @return array
	 */
	public static function dataNormalizeAttributesDomainUnsupportedType() {
		$types = TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);

		$data = [];
		foreach ($types as $key => $value) {
			$data['Attribute normalization: domain: unsupported type - ' . $key] = [
				'attributes' => [
					'domain' => $value['input'],
				],
				'expected'   => [],
			];
		}

		return $data;
	}

	/**
	 * Verify cookie attribute normalization works correctly when a positive (delta) max-age is provided.
	 *
	 * This test needs to be special cased as the use of time() makes it imprecise, so we need to test this with a less precise assertion.
	 *
	 * @return void
	 */
	public function testNormalizeAttributePositiveMaxAge() {
		$attributes = [
			'max-age' => '10',
		];
		$expected   = time() + 10;

		$cookie = new Cookie('requests-testcookie', 'testvalue', $attributes);

		$this->assertSame(array_keys($attributes), array_keys($cookie->attributes), 'Array keys are not the same');
		$this->assertEqualsWithDelta(
			$expected / 10,
			$cookie->attributes['max-age'] / 10,
			0.1, // Allow one second difference to prevent the test failing on time between function calls.
			'Max age not correctly set to current time + delta'
		);
	}
}
