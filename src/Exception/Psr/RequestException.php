<?php
/**
 * Transport Exception
 *
 * @package Requests\Exceptions
 */

namespace WpOrg\Requests\Exception\Psr;

use Psr\Http\Message\RequestInterface;
use WpOrg\Requests\Exception;

/**
 * Exception for when a request failed.
 *
 * @package Requests\Exceptions
 *
 * Examples:
 *      - Request is invalid (e.g. method is missing)
 *      - Runtime request errors (e.g. the body stream is not seekable)
 */
class RequestException extends ClientException/* implements \Psr\Http\Client\RequestExceptionInterface */ {

	/**
	 * @var RequestInterface
	 */
	private $request;

	/**
	 * Create a new exception
	 *
	 * @param RequestInterface $request
	 * @param Transport        $previous
	 */
	public function __construct(RequestInterface $request, Exception $previous) {
		parent::__construct(
			$previous->getMessage(),
			$previous->getType(),
			$previous->getData(),
			$previous->getCode()
		);

		$this->request = $request;
	}

	/**
	 * Returns the request.
	 *
	 * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
	 *
	 * @return RequestInterface
	 */
	public function getRequest() {
		return $this->request;
	}
}
