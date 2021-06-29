<?php

// First, include Requests
require_once dirname(__DIR__) . '/library/Requests.php';

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

// Now let's make a request!
$request = Requests::post('http://httpbin.org/post', array(), array('mydata' => 'something'));

// Check what we received
var_dump($request);
