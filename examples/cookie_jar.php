<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @package   Requests\Examples
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

// First, include the Requests Autoloader.
require_once dirname(__DIR__) . '/src/Autoload.php';

// Next, make sure Requests can load internal classes.
WpOrg\Requests\Autoload::register();

// Say you need to fake a login cookie
$c = new WpOrg\Requests\Cookie\Jar(['login_uid' => 'something']);

// Now let's make a request!
$request = WpOrg\Requests\Requests::get(
	'http://httpbin.org/cookies', // Url
	[],  // No need to set the headers the Jar does this for us
	['cookies' => $c] // Pass in the Jar as an option
);

// Check what we received
var_dump($request);
