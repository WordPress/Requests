<?php

// First, include Requests
require_once dirname(__DIR__) . '/library/Requests.php';

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

// Say you need to fake a login cookie
$c = new Requests_Cookie('login_uid', 'something');

// Now let's make a request!
$request = Requests::get('http://httpbin.org/cookies', array('Cookie' => $c->format_for_header()));

// Check what we received
var_dump($request);
