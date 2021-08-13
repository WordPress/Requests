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

// Temporarily silence the PSR-0 deprecations while the code is being switched to PSR-4.
define('REQUESTS_SILENCE_PSR0_DEPRECATIONS', true);

if (is_dir(dirname(__DIR__) . '/vendor')
	&& file_exists(dirname(__DIR__) . '/vendor/autoload.php')
	&& file_exists(dirname(__DIR__) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php')
) {
	$vendor_dir = dirname(__DIR__) . '/vendor';
} else {
	echo 'Please run `composer install` before attempting to run the unit tests.
You can still run the tests using a PHPUnit phar file, but some test dependencies need to be available.
';
	die(1);
}

if (defined('__PHPUNIT_PHAR__')) {
	// Testing via a PHPUnit phar.

	// Load the PHPUnit Polyfills autoloader.
	require_once $vendor_dir . '/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

	// Load the library autoloader.
	require_once dirname(__DIR__) . '/library/Requests.php';
	Requests::register_autoloader();

	/*
	 * Autoloader specifically for the test files.
	 * Fixes issues with PHPUnit not being able to find test classes being extended when running
	 * in a non-Composer context.
	 */
	spl_autoload_register(
		function ($class_name) {
			// Only try & load our own classes.
			if (stripos($class_name, 'Requests\\Tests\\') !== 0) {
				return false;
			}

			// Strip namespace prefix 'Requests\Tests\'.
			$relative_class = substr($class_name, 15);
			$file           = realpath(__DIR__ . '/' . strtr($relative_class, '\\', '/') . '.php');

			if (file_exists($file)) {
				include_once $file;
			}

			return true;
		}
	);
} else {
	// Testing via a Composer setup.
	require_once $vendor_dir . '/autoload.php';
}

function httpbin($suffix = '', $ssl = false) {
	$host = $ssl ? 'https://' . REQUESTS_TEST_HOST_HTTPS : 'http://' . REQUESTS_TEST_HOST_HTTP;
	return rtrim($host, '/') . '/' . ltrim($suffix, '/');
}
