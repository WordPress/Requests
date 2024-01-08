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
	 * Get an array of valid RFC 2616 token characters.
	 *
	 * Valid token as per RFC 2616 section 2.2:
	 * token = 1*<any CHAR except CTLs or separators>
	 *
	 * Disabling PHPCS checks for consistency with RFC 2616:
	 * phpcs:disable Squiz.PHP.CommentedOutCode.Found
	 * phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.ArrayItemNoNewLine
	 *
	 * @return array<string>
	 */
	private static function getValidTokenCharacters() {
		// CHAR = <any US-ASCII character (octets 0 - 127)>
		$rfc_char = array_map('chr', range(0, 127));

		// CTL = <any US-ASCII control character (octets 0 - 31) and DEL (127)>
		$rfc_ctl = array_map('chr', array_merge(range(0, 31), [127]));

		// SP = <US-ASCII SP, space (32)>
		$rfc_sp = chr(32);

		// HT = <US-ASCII HT, horizontal-tab (9)>
		$rfc_ht = chr(9);

		// separators = "(" | ")" | "<" | ">" | "@"
		//            | "," | ";" | ":" | "\" | <">
		//            | "/" | "[" | "]" | "?" | "="
		//            | "{" | "}" | SP | HT
		$rfc_separators = [
			'(', ')', '<', '>', '@',
			',', ';', ':', '\\', '"',
			'/', '[', ']', '?', '=',
			'{', '}', $rfc_sp, $rfc_ht,
		];

		// token characters = <any CHAR except CTLs or separators>
		return array_diff($rfc_char, $rfc_ctl, $rfc_separators);
	}

	/**
	 * Data Provider.
	 *
	 * Valid strings are valid tokens as per RFC 2616 section 2.2:
	 * token = 1*<any CHAR except CTLs or separators>
	 *
	 * @return array
	 */
	public static function dataValidStrings() {

		return [
			'string containing all valid token characters' => [
				'input' => implode(self::getValidTokenCharacters()),
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
		$invalid_ascii_characters = array_diff(
			array_map('chr', range(0, 127)),
			self::getValidTokenCharacters()
		);

		return [
			'empty string' => [
				'input' => '',
			],
			'string containing all invalid ASCII characters' => [
				'input' => implode($invalid_ascii_characters),
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
