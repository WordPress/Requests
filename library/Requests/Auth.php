<?php
namespace Rmccue\Requests;

use Rmccue\Requests\Hooks as Hooks;
/**
 * Authentication provider interface
 *
 * @package Rmccue\Requests
 * @subpackage Authentication
 */

/**
 * Authentication provider interface
 *
 * Implement this interface to act as an authentication provider.
 *
 * Parameters should be passed via the constructor where possible, as this
 * makes it much easier for users to use your provider.
 *
 * @see Rmccue\Requests\Hooks
 * @package Rmccue\Requests
 * @subpackage Authentication
 */
interface Auth {
	/**
	 * Register hooks as needed
	 *
	 * This method is called in {@see Requests::request} when the user has set
	 * an instance as the 'auth' option. Use this callback to register all the
	 * hooks you'll need.
	 *
	 * @see Requests_Hooks::register
	 * @param Rmccue\Requests\Hooks $hooks Hook system
	 */
	public function register(Hooks &$hooks);
}