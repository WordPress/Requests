<?php
/**
 * Requests for PHP
 *
 * Inspired by Requests for Python.
 *
 * Based on concepts from SimplePie_File, RequestCore and WP_Http.
 *
 * @package Requests
 */

require_once dirname(__DIR__) . '/src/Requests.php';

/**
 * Requests for PHP
 *
 * Inspired by Requests for Python.
 *
 * Based on concepts from SimplePie_File, RequestCore and WP_Http.
 *
 * @package Requests
 */
class Requests extends WpOrg\Requests\Requests {

	/**
	 * Deprecated autoloader for Requests.
	 *
	 * @deprecated 2.0.0 Use the `WpOrg\Requests\Autoload::load()` method instead.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $class Class name to load
	 */
	public static function autoloader($class) {
		if (class_exists('WpOrg\Requests\Autoload') === false) {
			require_once dirname(__DIR__) . '/src/Autoload.php';
		}

		return WpOrg\Requests\Autoload::load($class);
	}

	/**
	 * Register the built-in autoloader
	 *
	 * @deprecated 2.0.0 Include the `WpOrg\Requests\Autoload` class and
	 *                   call `WpOrg\Requests\Autoload::register()` instead.
	 *
	 * @codeCoverageIgnore
	 */
	public static function register_autoloader() {
		require_once dirname(__DIR__) . '/src/Autoload.php';
		WpOrg\Requests\Autoload::register();
	}
}
