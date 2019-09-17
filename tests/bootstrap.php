<?php

/**
 * Compatibility with PHPUnit 6+
 */
if (class_exists('PHPUnit\Runner\Version')) {
	require_once dirname(__FILE__) . '/phpunit6-compat.php';
}

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

require_once dirname(dirname(__FILE__)) . '/library/Requests.php';
Requests::register_autoloader();

function autoload_tests($class) {
	if (strpos($class, 'RequestsTest_') !== 0) {
		return;
	}

	$class = substr($class, 13);
	$file  = str_replace('_', '/', $class);
	if (file_exists(dirname(__FILE__) . '/' . $file . '.php')) {
		require_once dirname(__FILE__) . '/' . $file . '.php';
	}
}

spl_autoload_register('autoload_tests');

function httpbin($suffix = '', $ssl = false) {
	$host = $ssl ? 'https://' . REQUESTS_TEST_HOST_HTTPS : 'http://' . REQUESTS_TEST_HOST_HTTP;
	return rtrim($host, '/') . '/' . ltrim($suffix, '/');
}

class RawTransport implements Requests_Transport {
	public $data = '';
	public function request($url, $headers = array(), $data = array(), $options = array()) {
		return $this->data;
	}
	public function request_multiple($requests, $options) {
		foreach ($requests as $id => &$request) {
			$handler       = new RawTransport();
			$handler->data = $request['options']['raw.data'];
			$request       = $handler->request($request['url'], $request['headers'], $request['data'], $request['options']);
		}

		return $requests;
	}
	public static function test() {
		return true;
	}
}
