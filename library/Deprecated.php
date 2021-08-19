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
}

interface Requests_Auth extends WpOrg\Requests\Auth {}
interface Requests_Hooker extends WpOrg\Requests\Hooker {}
interface Requests_Proxy extends WpOrg\Requests\Proxy {}
interface Requests_Transport extends WpOrg\Requests\Transport {}

class Requests_Cookie extends WpOrg\Requests\Cookie {}
class Requests_Exception extends WpOrg\Requests\Exception {}
class Requests_Hooks extends WpOrg\Requests\Hooks {}
class Requests_IDNAEncoder extends WpOrg\Requests\IdnaEncoder {}
class Requests_IPv6 extends WpOrg\Requests\Ipv6 {}
class Requests_IRI extends WpOrg\Requests\Iri {}
class Requests_Response extends WpOrg\Requests\Response {}
class Requests_Session extends WpOrg\Requests\Session {}
class Requests_SSL extends WpOrg\Requests\Ssl {}

class Requests_Auth_Basic extends WpOrg\Requests\Auth\Basic {}
class Requests_Cookie_Jar extends WpOrg\Requests\Cookie\Jar {}
class Requests_Proxy_HTTP extends WpOrg\Requests\Proxy\Http {}
class Requests_Response_Headers extends WpOrg\Requests\Response\Headers {}
class Requests_Transport_cURL extends WpOrg\Requests\Transport\Curl {}
class Requests_Transport_fsockopen extends WpOrg\Requests\Transport\Fsockopen {}
class Requests_Utility_CaseInsensitiveDictionary extends WpOrg\Requests\Utility\CaseInsensitiveDictionary {}
class Requests_Utility_FilteredIterator extends WpOrg\Requests\Utility\FilteredIterator {}

class Requests_Exception_HTTP extends WpOrg\Requests\Exception\Http {}
class Requests_Exception_Transport extends WpOrg\Requests\Exception\Transport {}
class Requests_Exception_Transport_cURL extends WpOrg\Requests\Exception\Transport\Curl {}
class Requests_Exception_HTTP_304 extends WpOrg\Requests\Exception\Http\Status304 {}
class Requests_Exception_HTTP_305 extends WpOrg\Requests\Exception\Http\Status305 {}
class Requests_Exception_HTTP_306 extends WpOrg\Requests\Exception\Http\Status306 {}
class Requests_Exception_HTTP_400 extends WpOrg\Requests\Exception\Http\Status400 {}
class Requests_Exception_HTTP_401 extends WpOrg\Requests\Exception\Http\Status401 {}
class Requests_Exception_HTTP_402 extends WpOrg\Requests\Exception\Http\Status402 {}
class Requests_Exception_HTTP_403 extends WpOrg\Requests\Exception\Http\Status403 {}
class Requests_Exception_HTTP_404 extends WpOrg\Requests\Exception\Http\Status404 {}
class Requests_Exception_HTTP_405 extends WpOrg\Requests\Exception\Http\Status405 {}
class Requests_Exception_HTTP_406 extends WpOrg\Requests\Exception\Http\Status406 {}
class Requests_Exception_HTTP_407 extends WpOrg\Requests\Exception\Http\Status407 {}
class Requests_Exception_HTTP_408 extends WpOrg\Requests\Exception\Http\Status408 {}
class Requests_Exception_HTTP_409 extends WpOrg\Requests\Exception\Http\Status409 {}
class Requests_Exception_HTTP_410 extends WpOrg\Requests\Exception\Http\Status410 {}
class Requests_Exception_HTTP_411 extends WpOrg\Requests\Exception\Http\Status411 {}
class Requests_Exception_HTTP_412 extends WpOrg\Requests\Exception\Http\Status412 {}
class Requests_Exception_HTTP_413 extends WpOrg\Requests\Exception\Http\Status413 {}
class Requests_Exception_HTTP_414 extends WpOrg\Requests\Exception\Http\Status414 {}
class Requests_Exception_HTTP_415 extends WpOrg\Requests\Exception\Http\Status415 {}
class Requests_Exception_HTTP_416 extends WpOrg\Requests\Exception\Http\Status416 {}
class Requests_Exception_HTTP_417 extends WpOrg\Requests\Exception\Http\Status417 {}
class Requests_Exception_HTTP_418 extends WpOrg\Requests\Exception\Http\Status418 {}
class Requests_Exception_HTTP_428 extends WpOrg\Requests\Exception\Http\Status428 {}
class Requests_Exception_HTTP_429 extends WpOrg\Requests\Exception\Http\Status429 {}
class Requests_Exception_HTTP_431 extends WpOrg\Requests\Exception\Http\Status431 {}
class Requests_Exception_HTTP_500 extends WpOrg\Requests\Exception\Http\Status500 {}
class Requests_Exception_HTTP_501 extends WpOrg\Requests\Exception\Http\Status501 {}
class Requests_Exception_HTTP_502 extends WpOrg\Requests\Exception\Http\Status502 {}
class Requests_Exception_HTTP_503 extends WpOrg\Requests\Exception\Http\Status503 {}
class Requests_Exception_HTTP_504 extends WpOrg\Requests\Exception\Http\Status504 {}
class Requests_Exception_HTTP_505 extends WpOrg\Requests\Exception\Http\Status505 {}
class Requests_Exception_HTTP_511 extends WpOrg\Requests\Exception\Http\Status511 {}
class Requests_Exception_HTTP_Unknown extends WpOrg\Requests\Exception\Http\StatusUnknown {}
