<?php

/**
 * If you need to run the Requests v2.x library with another framework/CMS that still ships v1.x,
 * use the following to preload the class aliases before the actual implementations of Requests v1.x. get loaded.
 *
 * Please note that mixing Requests v1.x and v2.x files is a bad idea and may have side effects.
 * Do so at your own risk.
 */

// First, include the Requests Autoloader.
require_once 'path/to/Requests/src/Autoload.php';

// Make sure the autoloader is registered first as otherwise you may run into trouble on PHP 8.1.
// See: https://news-web.php.net/php.internals/115549
WpOrg\Requests\Autoload::register();

// Silence deprecations.
// Depending on when the bootstrapping is done, you may need to wrap this in a `if (!defined(...)) {}`.
define('REQUESTS_SILENCE_PSR0_DEPRECATIONS', true);

$preload = WpOrg\Requests\Autoload::get_deprecated_classes();
// Add the base Requests class, just to be safe.
$preload['requests'] = '\WpOrg\Requests\Requests';

// Preload the class aliases for the Requests 1.x classes to ensure only Requests 2.x classes get loaded.
foreach ($preload as $old => $new) {
	// Make sure we don't get "Class already exists errors" from autoloading chains
	// Think: an `implements` causing an interface to be loaded before we explicitly request it.
	if (class_exists($old) === false && interface_exists($old) === false) {
		WpOrg\Requests\Autoload::load($old);
	}
}
