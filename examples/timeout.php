<?php

// First, include the Requests Autoloader.
require_once dirname(__DIR__) . '/src/Autoload.php';

// Next, make sure Requests can load internal classes.
WpOrg\Requests\Autoload::register();

// Define a timeout of 2.5 seconds
$options = [
	'timeout' => 2.5,
];

// Now let's make a request to a page that will delay its response by 3 seconds
$request = WpOrg\Requests\Requests::get('http://httpbin.org/delay/3', [], $options);

// An exception will be thrown, stating a timeout of the request !
