<?php

date_default_timezone_set('UTC');

function define_from_env($name, $fallback = false) {
	$env = getenv($name);
	if ($env) {
		define($name, $env);
	}
	else {
		define($name, $fallback);
	}
}

define_from_env('REQUESTS_TEST_HOST', 'requests-php-tests.herokuapp.com');
define_from_env('REQUESTS_TEST_HOST_HTTP', REQUESTS_TEST_HOST);
define_from_env('REQUESTS_TEST_HOST_HTTPS', REQUESTS_TEST_HOST);

define_from_env('REQUESTS_HTTP_PROXY');
define_from_env('REQUESTS_HTTP_PROXY_AUTH');
define_from_env('REQUESTS_HTTP_PROXY_AUTH_USER');
define_from_env('REQUESTS_HTTP_PROXY_AUTH_PASS');


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
	require_once dirname(__DIR__) . '/src/Autoload.php';
	WpOrg\Requests\Autoload::register();

	/*
	 * Autoloader specifically for the test files.
	 * Fixes issues with PHPUnit not being able to find test classes being extended when running
	 * in a non-Composer context.
	 */
	spl_autoload_register(
		function ($class_name) {
			// Only try & load our own classes.
			if (stripos($class_name, 'WpOrg\\Requests\\Tests\\') !== 0) {
				return false;
			}

			// Strip namespace prefix 'WpOrg\\Requests\Tests\'.
			$relative_class = substr($class_name, 21);
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
