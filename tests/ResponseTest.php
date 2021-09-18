<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;

final class ResponseTest extends TestCase {

	public function testInvalidJsonResponse() {
		$this->expectException(Exception::class);

		$response       = new Response();
		$response->body = 'Invalid JSON';
		$response->json();
	}

	public function testJsonResponse() {
		$response       = new Response();
		$response->body = '{"success": false, "error": [], "data": null}';
		$decoded_body   = $response->json();

		$expected = array(
			'success' => false,
			'error'   => array(),
			'data'    => null,
		);

		foreach ($expected as $key => $value) {
			$this->assertEquals($value, $decoded_body[$key]);
		}
	}
}
