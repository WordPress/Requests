<?php

namespace Requests\Tests;

use Requests\Tests\TestCase;
use Requests_Exception_Transport_cURL;

class AutoloadTest extends TestCase {

	const MSG = 'The PSR-0 `Requests_...` class names in the Request library are deprecated.';

	/**
	 * Verify that a deprecation notice is thrown when the "old" Requests class is loaded.
	 */
	public function testDeprecationNoticeThrownForOldRequestsClass() {
		$this->expectDeprecation();
		$this->expectDeprecationMessage(self::MSG);

		require_once dirname(__DIR__) . '/library/Requests.php';
	}

	/**
	 * Verify that a deprecation notice is thrown when one of the other "old" Requests classes is autoloaded.
	 */
	public function testDeprecationNoticeThrownForOtherOldRequestsClass() {
		$this->expectDeprecation();
		$this->expectDeprecationMessage(self::MSG);

		$this->assertSame('cURLEasy', Requests_Exception_Transport_cURL::EASY);
	}
}
