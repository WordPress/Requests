<?php

// First, include the composer autoload.php.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Now let's make a request!
$httpClient = new WpOrg\Requests\Psr\HttpClient([]);

$request = $httpClient->createRequest('POST', 'http://httpbin.org/post');
$request = $request->withBody($httpClient->createStream(http_build_query(['mydata' => 'something'])));
$request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');

$response = $httpClient->sendRequest($request);

// Check what we received
var_dump($response);