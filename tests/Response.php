<?php

class RequestsTest_Response extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException Requests_Exception
     */
    public function testInvalidJsonResponse() {
        $response = new Requests_Response();
        $response->body = 'Invalid JSON';
        $response->json();
    }

    public function testJsonResponse() {
        $response = new Requests_Response();
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
