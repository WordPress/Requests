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
