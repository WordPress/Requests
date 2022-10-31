<?php

// First, include the composer autoload.php.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Say you need to fake a login cookie
$c = new WpOrg\Requests\Cookie('login_uid', 'something');

// Now let's make a request!
$httpClient = new WpOrg\Requests\Psr\HttpClient([]);

$request = $httpClient->createRequest('GET', 'http://httpbin.org/cookies');
$request = $request->withHeader('Cookie', $c->format_for_header());

$response = $httpClient->sendRequest($request);

// Check what we received
var_dump($response);
