<?php

define('REQUESTS_DIR', dirname(dirname(__FILE__)) . '/library');

function autoload_requests($class) {
	$file = str_replace('_', '/', $class);
	if (file_exists(REQUESTS_DIR . '/' . $file . '.php')) {
		require_once(REQUESTS_DIR . '/' . $file . '.php');
	}
}

function autoload_tests($class) {
	if (strpos($class, 'RequestsTest_') !== 0) {
		return;
	}

	$class = substr($class, 13);
	$file = str_replace('_', '/', $class);
	if (file_exists(dirname(__FILE__) . '/' . $file . '.php')) {
		require_once(dirname(__FILE__) . '/' . $file . '.php');
	}
}

spl_autoload_register('autoload_requests');
spl_autoload_register('autoload_tests');