<?php

// First, include the composer autoload.php.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Now let's make a request!
$httpClient = new WpOrg\Requests\Psr\HttpClient([]);

$request = $httpClient->createRequest('GET', 'http://httpbin.org/get');
$request = $request->withHeader('Accept', 'application/json');

$response = $httpClient->sendRequest($request);

// Check what we received
var_dump($response);
