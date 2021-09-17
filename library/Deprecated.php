<?php
/**
 * Backwards compatibility layer for Requests.
 *
 * Allows for Composer to autoload the old PSR-0 class names based on a classmap.
 *
 * All classes in this file are deprecated.
 * Please see the Changelog for the 2.0.0 release for upgrade notes.
 *
 * @package Requests
 *
 * @deprecated 2.0.0 Use the PSR-4 class names instead.
 */

/*
 * Integrators who cannot yet upgrade to the PSR-4 class names can silence deprecations
 * by defining a `REQUESTS_SILENCE_PSR0_DEPRECATIONS` constant and setting it to `true`.
 * The constant needs to be defined before the first deprecated class is requested
 * via this Composer autoload file.
 */
if (!defined('REQUESTS_SILENCE_PSR0_DEPRECATIONS') || REQUESTS_SILENCE_PSR0_DEPRECATIONS !== true) {
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
	trigger_error(
		'The PSR-0 `Requests_...` class names in the Request library are deprecated.'
		. ' Switch to the PSR-4 `WpOrg\Requests\...` class names at your earliest convenience.',
		E_USER_DEPRECATED
	);

	// Prevent the deprecation notice from being thrown twice.
	if (!defined('REQUESTS_SILENCE_PSR0_DEPRECATIONS')) {
		define('REQUESTS_SILENCE_PSR0_DEPRECATIONS', true);
	}
}

if (interface_exists('Requests_Auth') === false && interface_exists('WpOrg\Requests\Auth') === true) {
	interface Requests_Auth extends WpOrg\Requests\Auth {}
}
if (interface_exists('Requests_Hooker') === false && interface_exists('WpOrg\Requests\Hooker') === true) {
	interface Requests_Hooker extends WpOrg\Requests\Hooker {}
}
if (interface_exists('Requests_Proxy') === false && interface_exists('WpOrg\Requests\Proxy') === true) {
	interface Requests_Proxy extends WpOrg\Requests\Proxy {}
}
if (interface_exists('Requests_Transport') === false && interface_exists('WpOrg\Requests\Transport') === true) {
	interface Requests_Transport extends WpOrg\Requests\Transport {}
}

if (class_exists('Requests_Cookie') === false && class_exists('WpOrg\Requests\Cookie') === true) {
	class Requests_Cookie extends WpOrg\Requests\Cookie {}
}
if (class_exists('Requests_Exception') === false && class_exists('WpOrg\Requests\Exception') === true) {
	class Requests_Exception extends WpOrg\Requests\Exception {}
}
if (class_exists('Requests_Hooks') === false && class_exists('WpOrg\Requests\Hooks') === true) {
	class Requests_Hooks extends WpOrg\Requests\Hooks {}
}
if (class_exists('Requests_IDNAEncoder') === false && class_exists('WpOrg\Requests\IdnaEncoder') === true) {
	class Requests_IDNAEncoder extends WpOrg\Requests\IdnaEncoder {}
}
if (class_exists('Requests_IPv6') === false && class_exists('WpOrg\Requests\Ipv6') === true) {
	class Requests_IPv6 extends WpOrg\Requests\Ipv6 {}
}
if (class_exists('Requests_IRI') === false && class_exists('WpOrg\Requests\Iri') === true) {
	class Requests_IRI extends WpOrg\Requests\Iri {}
}
if (class_exists('Requests_Response') === false && class_exists('WpOrg\Requests\Response') === true) {
	class Requests_Response extends WpOrg\Requests\Response {}
}
if (class_exists('Requests_Session') === false && class_exists('WpOrg\Requests\Session') === true) {
	class Requests_Session extends WpOrg\Requests\Session {}
}
if (class_exists('Requests_SSL') === false && class_exists('WpOrg\Requests\Ssl') === true) {
	class Requests_SSL extends WpOrg\Requests\Ssl {}
}

if (class_exists('Requests_Auth_Basic') === false && class_exists('WpOrg\Requests\Auth\Basic') === true) {
	class Requests_Auth_Basic extends WpOrg\Requests\Auth\Basic {}
}
if (class_exists('Requests_Cookie_Jar') === false && class_exists('WpOrg\Requests\Cookie\Jar') === true) {
	class Requests_Cookie_Jar extends WpOrg\Requests\Cookie\Jar {}
}
if (class_exists('Requests_Proxy_HTTP') === false && class_exists('WpOrg\Requests\Proxy\Http') === true) {
	class Requests_Proxy_HTTP extends WpOrg\Requests\Proxy\Http {}
}
if (class_exists('Requests_Response_Headers') === false && class_exists('WpOrg\Requests\Response\Headers') === true) {
	class Requests_Response_Headers extends WpOrg\Requests\Response\Headers {}
}
if (class_exists('Requests_Transport_cURL') === false && class_exists('WpOrg\Requests\Transport\Curl') === true) {
	class Requests_Transport_cURL extends WpOrg\Requests\Transport\Curl {}
}
if (class_exists('Requests_Transport_fsockopen') === false && class_exists('WpOrg\Requests\Transport\Fsockopen') === true) {
	class Requests_Transport_fsockopen extends WpOrg\Requests\Transport\Fsockopen {}
}
if (class_exists('Requests_Utility_CaseInsensitiveDictionary') === false && class_exists('WpOrg\Requests\Utility\CaseInsensitiveDictionary') === true) {
	class Requests_Utility_CaseInsensitiveDictionary extends WpOrg\Requests\Utility\CaseInsensitiveDictionary {}
}
if (class_exists('Requests_Utility_FilteredIterator') === false && class_exists('WpOrg\Requests\Utility\FilteredIterator') === true) {
	class Requests_Utility_FilteredIterator extends WpOrg\Requests\Utility\FilteredIterator {}
}

if (class_exists('Requests_Exception_HTTP') === false && class_exists('WpOrg\Requests\Exception\Http') === true) {
	class Requests_Exception_HTTP extends WpOrg\Requests\Exception\Http {}
}
if (class_exists('Requests_Exception_Transport') === false && class_exists('WpOrg\Requests\Exception\Transport') === true) {
	class Requests_Exception_Transport extends WpOrg\Requests\Exception\Transport {}
}
if (class_exists('Requests_Exception_Transport_cURL') === false && class_exists('WpOrg\Requests\Exception\Transport\Curl') === true) {
	class Requests_Exception_Transport_cURL extends WpOrg\Requests\Exception\Transport\Curl {}
}
if (class_exists('Requests_Exception_HTTP_304') === false && class_exists('WpOrg\Requests\Exception\Http\Status304') === true) {
	class Requests_Exception_HTTP_304 extends WpOrg\Requests\Exception\Http\Status304 {}
}
if (class_exists('Requests_Exception_HTTP_305') === false && class_exists('WpOrg\Requests\Exception\Http\Status305') === true) {
	class Requests_Exception_HTTP_305 extends WpOrg\Requests\Exception\Http\Status305 {}
}
if (class_exists('Requests_Exception_HTTP_306') === false && class_exists('WpOrg\Requests\Exception\Http\Status306') === true) {
	class Requests_Exception_HTTP_306 extends WpOrg\Requests\Exception\Http\Status306 {}
}
if (class_exists('Requests_Exception_HTTP_400') === false && class_exists('WpOrg\Requests\Exception\Http\Status400') === true) {
	class Requests_Exception_HTTP_400 extends WpOrg\Requests\Exception\Http\Status400 {}
}
if (class_exists('Requests_Exception_HTTP_401') === false && class_exists('WpOrg\Requests\Exception\Http\Status401') === true) {
	class Requests_Exception_HTTP_401 extends WpOrg\Requests\Exception\Http\Status401 {}
}
if (class_exists('Requests_Exception_HTTP_402') === false && class_exists('WpOrg\Requests\Exception\Http\Status402') === true) {
	class Requests_Exception_HTTP_402 extends WpOrg\Requests\Exception\Http\Status402 {}
}
if (class_exists('Requests_Exception_HTTP_403') === false && class_exists('WpOrg\Requests\Exception\Http\Status403') === true) {
	class Requests_Exception_HTTP_403 extends WpOrg\Requests\Exception\Http\Status403 {}
}
if (class_exists('Requests_Exception_HTTP_404') === false && class_exists('WpOrg\Requests\Exception\Http\Status404') === true) {
	class Requests_Exception_HTTP_404 extends WpOrg\Requests\Exception\Http\Status404 {}
}
if (class_exists('Requests_Exception_HTTP_405') === false && class_exists('WpOrg\Requests\Exception\Http\Status405') === true) {
	class Requests_Exception_HTTP_405 extends WpOrg\Requests\Exception\Http\Status405 {}
}
if (class_exists('Requests_Exception_HTTP_406') === false && class_exists('WpOrg\Requests\Exception\Http\Status406') === true) {
	class Requests_Exception_HTTP_406 extends WpOrg\Requests\Exception\Http\Status406 {}
}
if (class_exists('Requests_Exception_HTTP_407') === false && class_exists('WpOrg\Requests\Exception\Http\Status407') === true) {
	class Requests_Exception_HTTP_407 extends WpOrg\Requests\Exception\Http\Status407 {}
}
if (class_exists('Requests_Exception_HTTP_408') === false && class_exists('WpOrg\Requests\Exception\Http\Status408') === true) {
	class Requests_Exception_HTTP_408 extends WpOrg\Requests\Exception\Http\Status408 {}
}
if (class_exists('Requests_Exception_HTTP_409') === false && class_exists('WpOrg\Requests\Exception\Http\Status409') === true) {
	class Requests_Exception_HTTP_409 extends WpOrg\Requests\Exception\Http\Status409 {}
}
if (class_exists('Requests_Exception_HTTP_410') === false && class_exists('WpOrg\Requests\Exception\Http\Status410') === true) {
	class Requests_Exception_HTTP_410 extends WpOrg\Requests\Exception\Http\Status410 {}
}
if (class_exists('Requests_Exception_HTTP_411') === false && class_exists('WpOrg\Requests\Exception\Http\Status411') === true) {
	class Requests_Exception_HTTP_411 extends WpOrg\Requests\Exception\Http\Status411 {}
}
if (class_exists('Requests_Exception_HTTP_412') === false && class_exists('WpOrg\Requests\Exception\Http\Status412') === true) {
	class Requests_Exception_HTTP_412 extends WpOrg\Requests\Exception\Http\Status412 {}
}
if (class_exists('Requests_Exception_HTTP_413') === false && class_exists('WpOrg\Requests\Exception\Http\Status413') === true) {
	class Requests_Exception_HTTP_413 extends WpOrg\Requests\Exception\Http\Status413 {}
}
if (class_exists('Requests_Exception_HTTP_414') === false && class_exists('WpOrg\Requests\Exception\Http\Status414') === true) {
	class Requests_Exception_HTTP_414 extends WpOrg\Requests\Exception\Http\Status414 {}
}
if (class_exists('Requests_Exception_HTTP_415') === false && class_exists('WpOrg\Requests\Exception\Http\Status415') === true) {
	class Requests_Exception_HTTP_415 extends WpOrg\Requests\Exception\Http\Status415 {}
}
if (class_exists('Requests_Exception_HTTP_416') === false && class_exists('WpOrg\Requests\Exception\Http\Status416') === true) {
	class Requests_Exception_HTTP_416 extends WpOrg\Requests\Exception\Http\Status416 {}
}
if (class_exists('Requests_Exception_HTTP_417') === false && class_exists('WpOrg\Requests\Exception\Http\Status417') === true) {
	class Requests_Exception_HTTP_417 extends WpOrg\Requests\Exception\Http\Status417 {}
}
if (class_exists('Requests_Exception_HTTP_418') === false && class_exists('WpOrg\Requests\Exception\Http\Status418') === true) {
	class Requests_Exception_HTTP_418 extends WpOrg\Requests\Exception\Http\Status418 {}
}
if (class_exists('Requests_Exception_HTTP_428') === false && class_exists('WpOrg\Requests\Exception\Http\Status428') === true) {
	class Requests_Exception_HTTP_428 extends WpOrg\Requests\Exception\Http\Status428 {}
}
if (class_exists('Requests_Exception_HTTP_429') === false && class_exists('WpOrg\Requests\Exception\Http\Status429') === true) {
	class Requests_Exception_HTTP_429 extends WpOrg\Requests\Exception\Http\Status429 {}
}
if (class_exists('Requests_Exception_HTTP_431') === false && class_exists('WpOrg\Requests\Exception\Http\Status431') === true) {
	class Requests_Exception_HTTP_431 extends WpOrg\Requests\Exception\Http\Status431 {}
}
if (class_exists('Requests_Exception_HTTP_500') === false && class_exists('WpOrg\Requests\Exception\Http\Status500') === true) {
	class Requests_Exception_HTTP_500 extends WpOrg\Requests\Exception\Http\Status500 {}
}
if (class_exists('Requests_Exception_HTTP_501') === false && class_exists('WpOrg\Requests\Exception\Http\Status501') === true) {
	class Requests_Exception_HTTP_501 extends WpOrg\Requests\Exception\Http\Status501 {}
}
if (class_exists('Requests_Exception_HTTP_502') === false && class_exists('WpOrg\Requests\Exception\Http\Status502') === true) {
	class Requests_Exception_HTTP_502 extends WpOrg\Requests\Exception\Http\Status502 {}
}
if (class_exists('Requests_Exception_HTTP_503') === false && class_exists('WpOrg\Requests\Exception\Http\Status503') === true) {
	class Requests_Exception_HTTP_503 extends WpOrg\Requests\Exception\Http\Status503 {}
}
if (class_exists('Requests_Exception_HTTP_504') === false && class_exists('WpOrg\Requests\Exception\Http\Status504') === true) {
	class Requests_Exception_HTTP_504 extends WpOrg\Requests\Exception\Http\Status504 {}
}
if (class_exists('Requests_Exception_HTTP_505') === false && class_exists('WpOrg\Requests\Exception\Http\Status505') === true) {
	class Requests_Exception_HTTP_505 extends WpOrg\Requests\Exception\Http\Status505 {}
}
if (class_exists('Requests_Exception_HTTP_511') === false && class_exists('WpOrg\Requests\Exception\Http\Status511') === true) {
	class Requests_Exception_HTTP_511 extends WpOrg\Requests\Exception\Http\Status511 {}
}
if (class_exists('Requests_Exception_HTTP_Unknown') === false && class_exists('WpOrg\Requests\Exception\Http\StatusUnknown') === true) {
	class Requests_Exception_HTTP_Unknown extends WpOrg\Requests\Exception\Http\StatusUnknown {}
}
