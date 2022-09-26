<?php
/**
 * HTTP client implementation for PSR-17
 *
 * @package Requests\Psr
 */

namespace WpOrg\Requests\Psr;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Exception\Psr\NetworkException;
use WpOrg\Requests\Exception\Psr\RequestException;
use WpOrg\Requests\Exception\Transport;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Requests;

/**
 * HTTP implementation for PSR-17 and PSR-18
 *
 * @package Requests\Psr
 */
final class HttpClient/* implements \Psr\Http\Message\RequestFactoryInterface, \Psr\Http\Message\StreamFactoryInterface, \Psr\Http\Client\ClientInterface */ {

	/**
	 * @var array
	 */
	private $options = [];

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct($options) {
		$this->options = $options;
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
	 * Create a new stream from a string.
	 *
	 * The stream SHOULD be created with a temporary resource.
	 *
	 * @param string $content String content with which to populate the stream.
	 * @return StreamInterface
	 */
	public function createStream($content = '') {
		if (!is_string($content)) {
			throw InvalidArgument::create(1, '$content', 'string', gettype($content));
		}

		return StringBasedStream::createFromString($content);
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
		try {
			$response = Requests::request(
				$request->getUri()->__toString(),
				$request->getHeaders(),
				$request->getBody()->__toString(),
				$request->getMethod(),
				$this->options
			);
		} catch (Transport $th) {
			throw new NetworkException($request, $th);
		} catch (Exception $th) {
			throw new RequestException($request, $th);
		}

		return Response::fromResponse($response);
	}
}
