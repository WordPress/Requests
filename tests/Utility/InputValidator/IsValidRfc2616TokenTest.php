<?php

namespace WpOrg\Requests\Tests\Utility\InputValidator;

use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\InputValidator;

/**
 * @covers \WpOrg\Requests\Utility\InputValidator::is_valid_rfc2616_token
 */
final class IsValidRfc2616TokenTest extends TestCase {

	/**
	 * Test whether a received input parameter is correctly identified as a valid RFC 2616 token.
	 *
	 * @dataProvider dataValidIntegers
	 * @dataProvider dataValidStrings
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testValid($input) {
		$this->assertTrue(InputValidator::is_valid_rfc2616_token($input));
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataValidIntegers() {
		return TypeProviderHelper::getSelection(TypeProviderHelper::GROUP_INT);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataValidStrings() {
		$all_valid_ascii = '!#$%&\'*+-.'; // Valid chars in ASCII 33-47 range.
		// No valid chars in ASCII 58-64 range.
		$all_valid_ascii .= '^_`'; // Valid chars in ASCII 91-96 range.
		$all_valid_ascii .= '|~'; // Valid chars in ASCII 123-126 range.

		for ($char = 48; $char <= 57; $char++) {
			// Chars 0-9.
			$all_valid_ascii .= chr($char);
		}

		for ($char = 65; $char <= 90; $char++) {
			// Chars A-Z.
			$all_valid_ascii .= chr($char);
		}

		for ($char = 97; $char <= 122; $char++) {
			// Chars a-z.
			$all_valid_ascii .= chr($char);
		}

		return [
			'string containing only valid ascii characters / all valid ascii characters' => [
				'input' => $all_valid_ascii,
			],
			'string with a typical cookie name' => [
				'input' => 'requests-testcookie',
			],
		];
	}

	/**
	 * Test whether a received input parameter is correctly identified as NOT a valid RFC 2616 token.
	 *
	 * @dataProvider dataInvalidTypes
	 * @dataProvider dataInvalidValues
	 *
	 * @param mixed $input Input parameter to verify.
	 *
	 * @return void
	 */
	public function testInvalid($input) {
		$this->assertFalse(InputValidator::is_valid_rfc2616_token($input));
	}

	/**
	 * Data Provider for invalid data types.
	 *
	 * @return array
	 */
	public static function dataInvalidTypes() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT, TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Data Provider for valid data types containing invalid values.
	 *
	 * @return array
	 */
	public static function dataInvalidValues() {
		$all_control = chr(127); // DEL.
		for ($char = 0; $char <= 31; $char++) {
			$all_control .= chr($char);
		}

		return [
			'empty string' => [
				'input' => '',
			],
			'string containing only control characters / all control characters' => [
				'input' => $all_control,
			],
			'string containing control character at start' => [
				'input' => chr(6) . 'some text',
			],
			'string containing control characters in text' => [
				'input' => "some\ntext\rwith\tcontrol\echaracters\fin\vit",
			],
			'string containing control character at end' => [
				'input' => 'some text' . chr(127),
			],
			'string containing only separator characters / all separator characters' => [
				'input' => '()<>@,;:\\"/[]?={} 	',
			],
			'string containing separator character at start' => [
				'input' => '=value',
			],
			'string containing separator characters in text' => [
				'input' => 'words "with" spaces and quotes',
			],
			'string containing separator character at end' => [
				'input' => 'punctuated;',
			],
			'string containing separator characters - leading and trailing whitespace' => [
				'input' => '	words    ',
			],
			'string containing non-ascii characters - Iñtërnâtiônàlizætiøn' => [
				'input' => 'Iñtërnâtiônàlizætiøn',
			],
			'string containing non-ascii characters - ௫' => [
				'input' => '௫', // Tamil digit five.
			],
		];
	}
}
