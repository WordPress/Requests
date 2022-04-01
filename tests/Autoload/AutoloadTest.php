<?php

namespace WpOrg\Requests\Tests\Autoload;

use Requests_Exception_Transport_cURL;
use Requests_Utility_FilteredIterator;
use WpOrg\Requests\Autoload;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Utility\FilteredIterator;

final class AutoloadTest extends TestCase {

	const MSG = 'The PSR-0 `Requests_...` class names in the Request library are deprecated.';

	/**
	 * Verify that a deprecation notice is thrown when the "old" Requests class is loaded.
	 */
	public function testDeprecationNoticeThrownForOldRequestsClass() {
		$this->expectDeprecation();
		$this->expectDeprecationMessage(self::MSG);

		require_once dirname(dirname(__DIR__)) . '/library/Requests.php';
	}

	/**
	 * Verify that a deprecation notice is thrown when one of the other "old" Requests classes is autoloaded.
	 */
	public function testDeprecationNoticeThrownForOtherOldRequestsClass() {
		$this->expectDeprecation();
		$this->expectDeprecationMessage(self::MSG);

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
	 * Verify that the constant declaration in the previous test doesn't affect other tests.
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
