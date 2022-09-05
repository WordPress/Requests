<?php
/**
 * HTTP client implementation for PSR-17
 *
 * @package Requests\Psr
 */

namespace WpOrg\Requests\Psr;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * HTTP implementation for PSR-17 and PSR-18
 *
 * @package Requests\Psr
 */
final class HttpClient/* implements \Psr\Http\Message\RequestFactoryInterface, \Psr\Http\Client\ClientInterface */ {
	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Create a new request.
	 *
	 * @param string $method The HTTP method associated with the request.
	 * @param UriInterface|string $uri The URI associated with the request.
	 */
	public function createRequest($method, $uri) {
		return new Request();
	}

	/**
	 * Sends a PSR-7 request and returns a PSR-7 response.
	 *
	 * @param RequestInterface $request
	 *
	 * @return ResponseInterface
	 *
	 * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
	 */
	public function sendRequest($request) {
		throw new Exception('not implemented');
	}
}
