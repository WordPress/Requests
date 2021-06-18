<?php

date_default_timezone_set('UTC');

function define_from_env($name, $default = false) {
	$env = getenv($name);
	if ($env) {
		define($name, $env);
	}
	else {
		define($name, $default);
	}
}

define_from_env('REQUESTS_TEST_HOST', 'requests-php-tests.herokuapp.com');
define_from_env('REQUESTS_TEST_HOST_HTTP', REQUESTS_TEST_HOST);
define_from_env('REQUESTS_TEST_HOST_HTTPS', REQUESTS_TEST_HOST);

define_from_env('REQUESTS_HTTP_PROXY');
define_from_env('REQUESTS_HTTP_PROXY_AUTH');
define_from_env('REQUESTS_HTTP_PROXY_AUTH_USER');
define_from_env('REQUESTS_HTTP_PROXY_AUTH_PASS');

require_once dirname(__DIR__) . '/library/Requests.php';
Requests::register_autoloader();

$polyfill_autoloader = dirname(__DIR__) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';
if (file_exists($polyfill_autoloader)) {
	require_once $polyfill_autoloader;
} else {
	echo 'Please run `composer install` before attempting to run the tests.', PHP_EOL;
	die(1);
}

function autoload_tests($class) {
	if (strpos($class, 'RequestsTest_') !== 0) {
		return;
	}

	$class = substr($class, 13);
	$file  = str_replace('_', '/', $class);
	if (file_exists(__DIR__ . '/' . $file . '.php')) {
		require_once __DIR__ . '/' . $file . '.php';
	}
}

spl_autoload_register('autoload_tests');

function httpbin($suffix = '', $ssl = false) {
	$host = $ssl ? 'https://' . REQUESTS_TEST_HOST_HTTPS : 'http://' . REQUESTS_TEST_HOST_HTTP;
	return rtrim($host, '/') . '/' . ltrim($suffix, '/');
}
