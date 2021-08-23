<?php

// First, include the Requests Autoloader.
require_once dirname(__DIR__) . '/src/Autoload.php';

// Next, make sure Requests can load internal classes.
WpOrg\Requests\Autoload::register();

// Now let's make a request!
$request = Requests::post('http://httpbin.org/post', array(), array('mydata' => 'something'));

// Check what we received
var_dump($request);
