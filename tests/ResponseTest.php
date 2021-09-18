<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;

class ResponseTest extends TestCase {

    public function testInvalidJsonResponse() {
        $this->expectException(Exception::class);

        $response = new Response();
        $response->body = 'Invalid JSON';
        $response->json();
    }

    public function testJsonResponse() {
        $response = new Response();
        $response->body = '{"success": false, "error": [], "data": null}';
        $decodedBody = $response->json();

        $expected = array(
            'success' => false,
            'error' => array(),
            'data' => null
        );

        foreach($expected as $key => $value)
        {
            $this->assertEquals($value, $decodedBody[$key]);
        }
    }
}
