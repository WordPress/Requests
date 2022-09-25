<?php
/**
 * PSR-7 Message Header implementation
 *
 * @package Requests\Psr
 */

namespace WpOrg\Requests\Psr;

use Psr\Http\Message\RequestInterface;
use WpOrg\Requests\Exception\InvalidArgument;

/**
 * PPSR-7 Message Header implementation
 *
 * @package Requests\Psr
 */
trait MessageHeaderTrait {

	/**
	 * @var array
	 */
	private $headers = [];

	/**
	 * @var array
	 */
	private $headerNames = [];

	/**
	 * Retrieves all message header values.
	 *
	 * The keys represent the header name as it will be sent over the wire, and
	 * each value is an array of strings associated with the header.
	 *
	 *     // Represent the headers as a string
	 *     foreach ($message->getHeaders() as $name => $values) {
	 *         echo $name . ': ' . implode(', ', $values);
	 *     }
	 *
	 *     // Emit headers iteratively:
	 *     foreach ($message->getHeaders() as $name => $values) {
	 *         foreach ($values as $value) {
	 *             header(sprintf('%s: %s', $name, $value), false);
	 *         }
	 *     }
	 *
	 * While header names are not case-sensitive, getHeaders() will preserve the
	 * exact case in which headers were originally specified.
	 *
	 * @return string[][] Returns an associative array of the message's headers.
	 *     Each key MUST be a header name, and each value MUST be an array of
	 *     strings for that header.
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * Checks if a header exists by the given case-insensitive name.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return bool Returns true if any header names match the given header
	 *     name using a case-insensitive string comparison. Returns false if
	 *     no matching header name is found in the message.
	 */
	public function hasHeader($name) {
		if (!is_string($name)) {
			throw InvalidArgument::create(1, '$name', 'string', gettype($name));
		}

		return array_key_exists(strtolower($name), $this->headerNames);
	}

	/**
	 * Retrieves a message header value by the given case-insensitive name.
	 *
	 * This method returns an array of all the header values of the given
	 * case-insensitive header name.
	 *
	 * If the header does not appear in the message, this method MUST return an
	 * empty array.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string[] An array of string values as provided for the given
	 *    header. If the header does not appear in the message, this method MUST
	 *    return an empty array.
	 */
	public function getHeader($name) {
		if (!is_string($name)) {
			throw InvalidArgument::create(1, '$name', 'string', gettype($name));
		}

		if (!array_key_exists(strtolower($name), $this->headers)) {
			return [];
		}

		return $this->headers[$this->headerNames[strtolower($name)]];
	}

	/**
	 * Retrieves a comma-separated string of the values for a single header.
	 *
	 * This method returns all of the header values of the given
	 * case-insensitive header name as a string concatenated together using
	 * a comma.
	 *
	 * NOTE: Not all header values may be appropriately represented using
	 * comma concatenation. For such headers, use getHeader() instead
	 * and supply your own delimiter when concatenating.
	 *
	 * If the header does not appear in the message, this method MUST return
	 * an empty string.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string A string of values as provided for the given header
	 *    concatenated together using a comma. If the header does not appear in
	 *    the message, this method MUST return an empty string.
	 */
	public function getHeaderLine($name) {
		if (!is_string($name)) {
			throw InvalidArgument::create(1, '$name', 'string', gettype($name));
		}

		if (!array_key_exists(strtolower($name), $this->headerNames)) {
			return '';
		}

		return implode(',', $this->headers[$this->headerNames[strtolower($name)]]);
	}

	/**
	 * Return an instance with the provided value replacing the specified header.
	 *
	 * While header names are case-insensitive, the casing of the header will
	 * be preserved by this function, and returned from getHeaders().
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new and/or updated header and value.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @param string|string[] $value Header value(s).
	 * @return static
	 * @throws \InvalidArgumentException for invalid header names or values.
	 */
	public function withHeader($name, $value) {
		if (!is_string($name)) {
			throw InvalidArgument::create(1, '$name', 'string', gettype($name));
		}

		if (!is_string($value) && !is_array($value)) {
			throw InvalidArgument::create(2, '$value', 'string|array containing strings', gettype($value));
		}

		if (!is_array($value)) {
			$value = [$value];
		}

		foreach ($value as $line) {
			if (!is_string($line)) {
				throw InvalidArgument::create(2, '$value', 'string|array containing strings', gettype($value));
			}
		}

		$return = clone($this);
		$return->updateHeader($name, $value);

		return $return;
	}

	/**
	 * Return an instance with the specified header appended with the given value.
	 *
	 * Existing values for the specified header will be maintained. The new
	 * value(s) will be appended to the existing list. If the header did not
	 * exist previously, it will be added.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new header and/or value.
	 *
	 * @param string $name Case-insensitive header field name to add.
	 * @param string|string[] $value Header value(s).
	 * @return static
	 * @throws \InvalidArgumentException for invalid header names.
	 * @throws \InvalidArgumentException for invalid header values.
	 */
	public function withAddedHeader($name, $value) {
		if (!is_string($name)) {
			throw InvalidArgument::create(1, '$name', 'string', gettype($name));
		}

		if (!is_string($value) && !is_array($value)) {
			throw InvalidArgument::create(2, '$value', 'string|array containing strings', gettype($value));
		}

		if (!is_array($value)) {
			$value = [$value];
		}

		foreach ($value as $line) {
			if (!is_string($line)) {
				throw InvalidArgument::create(2, '$value', 'string|array containing strings', gettype($value));
			}
		}

		$return = clone($this);

		$return->updateHeader($name, array_merge($return->getHeader($name), $value));

		return $return;
	}

	/**
	 * Return an instance without the specified header.
	 *
	 * Header resolution MUST be done without case-sensitivity.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that removes
	 * the named header.
	 *
	 * @param string $name Case-insensitive header field name to remove.
	 * @return static
	 */
	public function withoutHeader($name) {
		if (!is_string($name)) {
			throw InvalidArgument::create(1, '$name', 'string', gettype($name));
		}

		$return = clone($this);
		$return->updateHeader($name, []);

		return $return;
	}

	/**
	 * Set, update or remove a header.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @param string[] $values Header value(s) or empty array to remove the header.
	 * @return void
	 */
	private function updateHeader($name, $values) {
		$headerName = strtolower($name);

		if (array_key_exists($headerName, $this->headerNames)) {
			unset($this->headers[$this->headerNames[$headerName]]);
			unset($this->headerNames[$headerName]);
		}

		if ($values === []) {
			return;
		}

		// Since the Host field-value is critical information for handling a
		// request, a user agent SHOULD generate Host as the first header field
		// following the request-line.
		// @see https://www.rfc-editor.org/rfc/rfc7230#section-5.4
		if ($this instanceof RequestInterface && $headerName === 'host') {
			$this->headers = [$name => []] + $this->headers;
		}

		$this->headers[$name] = $values;
		$this->headerNames[$headerName] = $name;
	}
}
