<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * Backwards compatibility layer for Requests.
 *
 * Allows for Composer to autoload the old PSR-0 classes via the custom autoloader.
 * This prevents issues with _extending final classes_ (which was the previous solution).
 *
 * Please see the Changelog for the 2.0.0 release for upgrade notes.
 *
 * @package Requests
 *
 * @deprecated 2.0.0 Use the PSR-4 class names instead.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

if (class_exists('WpOrg\Requests\Autoload') === false) {
	require_once dirname(__DIR__) . '/src/Autoload.php';
}

WpOrg\Requests\Autoload::register();
