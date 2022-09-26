<?php
/**
 * Transport Exception
 *
 * @package Requests\Exceptions
 */

namespace WpOrg\Requests\Exception\Psr;

use Psr\Http\Message\RequestInterface;
use WpOrg\Requests\Exception\Transport;

/**
 * Network Exception
 *
 * @package Requests\Exceptions
 *
 * Thrown when the request cannot be completed because of network issues.
 *
 * There is no response object as this exception is thrown when no response has been received.
 *
 * Example: the target host name can not be resolved or the connection failed.
 */
class NetworkException extends ClientException/* implements \Psr\Http\Client\NetworkExceptionInterface */ {

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
	public function __construct(RequestInterface $request, Transport $previous) {
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
