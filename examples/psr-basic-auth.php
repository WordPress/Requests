<?php

// First, include the composer autoload.php.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Now let's make a request!
$httpClient = new WpOrg\Requests\Psr\HttpClient([
	'auth' => ['someuser', 'password'],
]);

$request = $httpClient->createRequest('GET', 'http://httpbin.org/basic-auth/someuser/password');

$response = $httpClient->sendRequest($request);

// Check what we received
var_dump($response);
