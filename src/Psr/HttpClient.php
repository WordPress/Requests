<?php
/**
 * HTTP client implementation for PSR-17
 *
 * @package Requests\Psr
 */

namespace WpOrg\Requests\Psr;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;

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
		if (! $uri instanceof UriInterface) {
			if (!is_string($uri)) {
				throw InvalidArgument::create(2, '$uri', UriInterface::class.'|string', gettype($uri));
			}

			$uri = Uri::fromIri(new Iri($uri));
		}

		return Request::withMethodAndUri($method, $uri);
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
		return new Response();
	}
}
