<?php

// First, include Requests
include('../library/Requests.php');

// Next, make sure Requests can load internal classes
\Rmccue\Requests::register_autoloader();

// Now let's make a request!
$request = \Rmccue\Requests::post('http://httpbin.org/post', array(), array('mydata' => 'something'));

// Check what we received
var_dump($request);