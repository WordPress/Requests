<?php

// First, include Requests
include('../library/Requests.php');

// Next, make sure Requests can load internal classes
\Rmccue\Requests::register_autoloader();

// Now let's make a request!
$request = \Rmccue\Requests::get('http://httpbin.org/get', array('Accept' => 'application/json'));

// Check what we received
var_dump($request);