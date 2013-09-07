<?php

// First, include Requests
include('../library/Requests.php');

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

// Let's make a direct request
$request = Requests::get('http://httpbin.org/ip' );

// Check what we received
var_dump( $request->body );

// Now let's make a request via a proxy
$options = array(
	'proxy' => '95.65.58.61:443',
);
$request = Requests::get('http://httpbin.org/ip', array(), $options );

// Compare with previous result
var_dump( $request->body );