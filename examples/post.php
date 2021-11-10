<?php

// First, include the Requests Autoloader.
require_once dirname(__DIR__) . '/src/Autoload.php';

// Next, make sure Requests can load internal classes.
WpOrg\Requests\Autoload::register();

// Now let's make a request!
$request = WpOrg\Requests\Requests::post('http://httpbin.org/post', array(), array('mydata' => 'something'));

// Check what we received
var_dump($request);
// create temp file for example
file_put_contents( $tmpfile = tempnam(sys_get_temp_dir(), 'requests' ), 'hello');

// Now let's make a request!
$body = \WpOrg\Requests\Requests::add_files_to_body( array('mydata' => 'something'), $tmpfile, 'file1'  );
$request = WpOrg\Requests\Requests::post('http://httpbin.org/post', array(), $body );

// Check what we received
var_dump($request);
