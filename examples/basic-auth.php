<?php

// First, include Requests
require_once dirname(dirname(__FILE__)) . '/library/Requests.php';

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

// Now let's make a request!
$options = array(
	'auth' => array('someuser', 'password'),
);
$request = Requests::get('http://httpbin.org/basic-auth/someuser/password', array(), $options);

// Check what we received
var_dump($request);
