<?php
/**
 * Bootstrap file for PHPStan.
 *
 * The Composer autoloader registers the autoloader for the deprecated PSR-0 `Requests_*` class
 * names (see library/Deprecated.php). When PHPStan reflects on those class names, which the
 * autoloader tests reference, that autoloader triggers an E_USER_DEPRECATED notice, which PHPStan
 * reports as an internal error. The constant below is the documented way to silence the notice and
 * only affects the analysis run.
 */

if (defined('REQUESTS_SILENCE_PSR0_DEPRECATIONS') === false) {
	define('REQUESTS_SILENCE_PSR0_DEPRECATIONS', true);
}
