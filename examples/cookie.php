<?php

// First, include the Requests Autoloader.
require_once dirname(__DIR__) . '/src/Autoload.php';

// Next, make sure Requests can load internal classes.
WpOrg\Requests\Autoload::register();

// Say you need to fake a login cookie
$c = new Requests_Cookie('login_uid', 'something');

// Now let's make a request!
$request = Requests::get('http://httpbin.org/cookies', array('Cookie' => $c->format_for_header()));

// Check what we received
var_dump($request);
