<?php
/**
 * HTTP client implementation for PSR-17
 *
 * @package Requests\Psr
 */

namespace WpOrg\Requests\Psr;

use Exception;

/**
 * HTTP client implementation for PSR-17
 *
 * @package Requests\Psr
 */
final class HttpClient/* implements \Psr\Http\Message\ServerRequestFactoryInterface */ {
	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Create a new server request.
	 *
	 * Note that server parameters are taken precisely as given - no parsing/processing
	 * of the given values is performed. In particular, no attempt is made to
	 * determine the HTTP method or URI, which must be provided explicitly.
	 *
	 * @param string $method The HTTP method associated with the request.
	 * @param \Psr\Http\Message\UriInterface|string $uri The URI associated with the request.
	 * @param array $serverParams An array of Server API (SAPI) parameters with
	 *     which to seed the generated request instance.
	 *
	 * @return \Psr\Http\Message\ServerRequestInterface
	 */
	public function createServerRequest($method, $uri, $serverParams = [])
	{
		throw new Exception('not implemented');
	}
}
