<?php

namespace WpOrg\Requests\Tests\Autoload;

use Exception;
use Requests;
use Requests_Exception_Transport_cURL;
use Requests_Utility_FilteredIterator;
use WpOrg\Requests\Autoload;
use WpOrg\Requests\Exception\Http\Status417;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\FilteredIterator;

/**
 * @covers \WpOrg\Requests\Autoload
 */
final class AutoloadTest extends TestCase {

	const MSG = 'The PSR-0 `Requests_...` class names in the Requests library are deprecated.';

	/**
	 * Verify that a deprecation notice is thrown when the "old" Requests class is loaded via a require/include.
	 */
	public function testDeprecationNoticeThrownForOldRequestsClass() {
		// PHPUnit 10 compatible way to test the deprecation notice.
		set_error_handler(
			static function ($errno, $errstr) {
				restore_error_handler();
				throw new Exception($errstr, $errno);
			},
			E_USER_DEPRECATED
		);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage(self::MSG);

		require_once dirname(dirname(__DIR__)) . '/library/Requests.php';
	}

	/**
	 * Verify that a deprecation notice is thrown when one of the other "old" Requests classes is autoloaded.
	 */
	public function testDeprecationNoticeThrownForOtherOldRequestsClass() {
		// PHPUnit 10 compatible way to test the deprecation notice.
		set_error_handler(
			static function ($errno, $errstr) {
				restore_error_handler();
				throw new Exception($errstr, $errno);
			},
			E_USER_DEPRECATED
		);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage(self::MSG);

		$this->assertNotEmpty(Requests_Exception_Transport_cURL::EASY);
	}

	/**
	 * Verify that the deprecation layer works without a fatal error for extending a final class.
	 *
	 * Note: this test also verifies that the PSR-0 names are handled case-insensitively by the autoloader.
	 *
	 * @preserveGlobalState disabled
	 * @runInSeparateProcess
	 */
	public function testAutoloadOfOldRequestsClassDoesNotThrowAFatalForFinalClass() {
		define('REQUESTS_SILENCE_PSR0_DEPRECATIONS', true);

		$this->assertInstanceOf(FilteredIterator::class, new Requests_utility_filteredIterator([], static function() {}));
	}

	/**
	 * Perfunctory test of the register() method.
	 *
	 * Note: "perfunctory" as the test bootstrap already registers the autoloader.
	 *
	 * @preserveGlobalState disabled
	 * @runInSeparateProcess
	 */
	public function testRegister() {
		Autoload::register();

		$this->assertContains([Autoload::class, 'load'], spl_autoload_functions(), 'Autoload method is not registered.');
		$this->assertTrue(defined('REQUESTS_AUTOLOAD_REGISTERED'), 'Constant is not declared');
		$this->assertTrue(REQUESTS_AUTOLOAD_REGISTERED, 'Constant is not set to true');
	}

	/**
	 * Test the load() method returns the correct boolean response to allow for autoload-chaining.
	 *
	 * @dataProvider dataLoad
	 *
	 * @preserveGlobalState disabled
	 * @runInSeparateProcess
	 *
	 * @param string $class_name The class to load.
	 * @param bool   $expected   Expected function return value.
	 *
	 * @return void
	 */
	public function testLoad($class_name, $expected) {
		define('REQUESTS_SILENCE_PSR0_DEPRECATIONS', true);

		$this->assertSame($expected, Autoload::load($class_name));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public static function dataLoad() {
		return [
			'Request for class not in this package should be rejected' => [
				'class_name' => 'Unrelated\Package\ClassName',
				'expected'   => false,
			],
			'Request for PSR-0 class not in this package but using the `Requests_` prefix should be rejected' => [
				'class_name' => 'Requests_This_Class_Doesnt_Exist',
				'expected'   => false,
			],
			'Request for PSR-0 Requests class from this package should be accepted' => [
				'class_name' => Requests::class,
				'expected'   => true,
			],
			'Request for other PSR-0 Requests class from this package should be accepted' => [
				'class_name' => 'Requests_Exception_HTTP_429',
				'expected'   => true,
			],
			'Request for PSR-4 class not in this package but using the Requests namespace should be rejected' => [
				'class_name' => 'WpOrg\\Requests\\ThisClassDoesntExist',
				'expected'   => false,
			],
			'Request for PSR-4 class from this package should be accepted' => [
				'class_name' => Status417::class,
				'expected'   => true,
			],
		];
	}

	/**
	 * Verify that the constant declaration in the previous test(s) doesn't affect other tests.
	 *
	 * @coversNothing
	 */
	public function testConstantDeclarationDoesntInfluenceFurtherTests() {
		$this->assertFalse(defined('REQUESTS_SILENCE_PSR0_DEPRECATIONS'));
	}

	/**
	 * Verify that the get_deprecated_classes() method returns an array with 57 items.
	 */
	public function testGetDeprecatedClassesReturnsAnArrayWithCorrectAmountOfItems() {
		$this->assertCount(57, Autoload::get_deprecated_classes());
	}
}
