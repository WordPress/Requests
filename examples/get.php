<?php

// First, include Requests
require_once dirname(dirname(__FILE__)) . '/library/Requests.php';

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

// Now let's make a request!
$request = Requests::get('http://httpbin.org/get', array('Accept' => 'application/json'));

// Check what we received
var_dump($request);
