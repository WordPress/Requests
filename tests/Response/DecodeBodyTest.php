<?php

namespace WpOrg\Requests\Tests\Response;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Response::decode_body
 *
 * @requires extension json
 */
final class DecodeBodyTest extends TestCase {

	/**
	 * Verify that an exception is thrown when the body content is invalid as JSON.
	 *
	 * @dataProvider dataInvalidJsonResponse
	 *
	 * @param mixed $body Data to use as the Response body.
	 *
	 * @return void
	 */
	public function testInvalidJsonResponse($body) {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Unable to parse JSON data: ');

		$response       = new Response();
		$response->body = $body;

		$response->decode_body();
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataInvalidJsonResponse() {
		$data = [
			'text string, not JSON (syntax error)'       => ['Invalid JSON'],
			'invalid JSON: single quotes (syntax error)' => ["{ 'bar': 'baz' }"],
		];

		// An empty string is only regarded as invalid JSON since PHP 7.0.
		if (PHP_VERSION_ID >= 70000) {
			$data['empty string (syntax error)'] = [''];
		}

		return $data;
	}

	/**
	 * Verify correctly decoding a body in valid JSON.
	 *
	 * @return void
	 */
	public function testJsonResponse() {
		$response       = new Response();
		$response->body = '{"success": false, "error": [], "data": null}';
		$decoded_body   = $response->decode_body();

		$expected = [
			'success' => false,
			'error'   => [],
			'data'    => null,
		];

		$this->assertSame($expected, $decoded_body);
	}
}
