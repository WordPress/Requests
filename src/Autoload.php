<?php
/**
 * Autoloader for Requests for PHP.
 *
 * Include this file if you'd like to avoid having to create your own autoloader.
 *
 * @package Requests
 * @since   2.0.0
 *
 * @codeCoverageIgnore
 */

namespace WpOrg\Requests;

/*
 * Ensure the autoloader is only declared once.
 * This safeguard is in place as this is the typical entry point for this library
 * and this file being required unconditionally could easily cause
 * fatal "Class already declared" errors.
 */
if (class_exists('WpOrg\Requests\Autoload') === false) {

	/**
	 * Autoloader for Requests for PHP.
	 *
	 * @package Requests
	 */
	class Autoload {

		/**
		 * Register the autoloader.
		 *
		 * Note: the autoloader is *prepended* in the autoload queue.
		 * This is done to ensure that the Requests 2.0 autoloader takes precedence
		 * over a potentially (dependency-registered) Requests 1.x autoloader.
		 *
		 * @internal This method contains a safeguard against the autoloader being
		 * registered multiple times. This safeguard uses a global constant to
		 * (hopefully/in most cases) still function correctly, even if the
		 * class would be renamed.
		 *
		 * @return void
		 */
		public static function register() {
			if (defined('REQUESTS_AUTOLOAD_REGISTERED') === false) {
				spl_autoload_register(array(self::class, 'load'), true);
				define('REQUESTS_AUTOLOAD_REGISTERED', true);
			}
		}

		/**
		 * Autoloader.
		 *
		 * @param string $class_name Name of the class name to load.
		 */
		public static function load($class_name) {
			// Check that the class starts with "Requests".
			if (strpos($class_name, 'Requests') !== 0) {
				return;
			}

			$file = str_replace('_', '/', $class_name);
			if (file_exists(dirname(__DIR__) . '/library/' . $file . '.php')) {
				require_once dirname(__DIR__) . '/library/' . $file . '.php';
			}
		}
	}
}
