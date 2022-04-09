<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\Http;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response::throw_for_status
 */
final class ThrowForStatusTest extends TestCase {

	/**
	 * Verify that a redirection does not lead to an exception when redirection is allowed
	 * based on the default value of the `$allow_redirects` parameter.
	 *
	 * @doesNotPerformAssertions
	 */
	public function testRedirectWithDefaultParamValue() {
		$response              = new Response();
		$response->status_code = 302;

		$response->throw_for_status();
	}

	/**
	 * Verify that a redirection does not lead to an exception when redirection is allowed
	 * when explicitly passing the `$allow_redirects` parameter.
	 *
	 * @doesNotPerformAssertions
	 */
	public function testRedirectAndAllowed() {
		$response              = new Response();
		$response->status_code = 302;

		$response->throw_for_status(true);
	}

	/**
	 * Verify an exception is thrown when redirection is not allowed and the response has a redirection status.
	 */
	public function testRedirectAndNotAllowed() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Redirection not allowed');

		$response              = new Response();
		$response->status_code = 307;

		$response->throw_for_status(false);
	}

	/**
	 * Verify that an exception is thrown for unsuccessful requests.
	 */
	public function testNonRedirectFailedRequest() {
		$this->expectException(Http::class);

		$response              = new Response();
		$response->status_code = 502;
		$response->success     = false;

		$response->throw_for_status(true);
	}

	/**
	 * Verify that a successful, non-redirection request does not lead to an exception being thrown.
	 *
	 * @doesNotPerformAssertions
	 */
	public function testNonRedirectSuccessfulRequest() {
		$response              = new Response();
		$response->status_code = 200;
		$response->success     = true;

		$response->throw_for_status(true);
	}
}
