<?php
/**
 * PSR-7 ResponseInterface implementation
 *
 * @package Requests\Psr
 */

namespace WpOrg\Requests\Psr;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WpOrg\Requests\Response as RequestsResponse;

/**
 * PSR-7 ResponseInterface implementation
 *
 * @package Requests\Psr
 *
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
final class Response implements ResponseInterface {

	/**
	 * create Response
	 *
	 * @param RequestsResponse $response
	 *
	 * @return Response
	 */
	public static function fromResponse(RequestsResponse $response) {
		if ($response->protocol_version === false) {
			$protocol_version = '1.1';
		} else {
			$protocol_version = number_format($response->protocol_version, 1, '.', '');
		}

		return new self(
			StringBasedStream::createFromString($response->body),
			$response->status_code,
			$protocol_version
		);
	}

	/**
	 * @var StreamInterface
	 */
	private $body;

	/**
	 * @var int
	 */
	private $status_code;

	/**
	 * @var string
	 */
	private $protocol_version;

	/**
	 * All reason phrases.
	 *
	 * @see https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 * Last Updated 2022-06-08
	 *
	 * @var array
	 */
	private $reasonPhrases = [
		100 => 'Continue',                        // RFC9110, Section 15.2.1
		101 => 'Switching Protocols',             // RFC9110, Section 15.2.2
		102 => 'Processing',                      // RFC2518
		103 => 'Early Hints',                     // RFC2518
		200 => 'OK',                              // RFC9110, Section 15.3.1
		201 => 'Created',                         // RFC9110, Section 15.3.2
		202 => 'Accepted',                        // RFC9110, Section 15.3.3
		203 => 'Non-Authoritative Information',   // RFC9110, Section 15.3.4
		204 => 'No Content',                      // RFC9110, Section 15.3.5
		205 => 'Reset Content',                   // RFC9110, Section 15.3.6
		206 => 'Partial Content',                 // RFC9110, Section 15.3.7
		207 => 'Multi-Status',                    // RFC4918
		208 => 'Already Reported',                // RFC5842
		226 => 'IM Used',                         // RFC3229
		300 => 'Multiple Choices',                // RFC9110, Section 15.4.1
		301 => 'Moved Permanently',               // RFC9110, Section 15.4.2
		302 => 'Found',                           // RFC9110, Section 15.4.3
		303 => 'See Other',                       // RFC9110, Section 15.4.4
		304 => 'Not Modified',                    // RFC9110, Section 15.4.5
		305 => 'Use Proxy',                       // RFC9110, Section 15.4.6
		307 => 'Temporary Redirect',              // RFC9110, Section 15.4.8
		308 => 'Permanent Redirect',              // RFC9110, Section 15.4.9
		400 => 'Bad Request',                     // RFC9110, Section 15.5.1
		401 => 'Unauthorized',                    // RFC9110, Section 15.5.2
		402 => 'Payment Required',                // RFC9110, Section 15.5.3
		403 => 'Forbidden',                       // RFC9110, Section 15.5.4
		404 => 'Not Found',                       // RFC9110, Section 15.5.5
		405 => 'Method Not Allowed',              // RFC9110, Section 15.5.6
		406 => 'Not Acceptable',                  // RFC9110, Section 15.5.7
		407 => 'Proxy Authentication Required',   // RFC9110, Section 15.5.8
		408 => 'Request Timeout',                 // RFC9110, Section 15.5.9
		409 => 'Conflict',                        // RFC9110, Section 15.5.10
		410 => 'Gone',                            // RFC9110, Section 15.5.11
		411 => 'Length Required',                 // RFC9110, Section 15.5.12
		412 => 'Precondition Failed',             // RFC9110, Section 15.5.13
		413 => 'Content Too Large',               // RFC9110, Section 15.5.14
		414 => 'URI Too Long',                    // RFC9110, Section 15.5.15
		415 => 'Unsupported Media Type',          // RFC9110, Section 15.5.16
		416 => 'Range Not Satisfiable',           // RFC9110, Section 15.5.17
		417 => 'Expectation Failed',              // RFC9110, Section 15.5.18
		418 => 'I\'m a teapot',                   // RFC2324
		421 => 'Misdirected Request',             // RFC7540
		422 => 'Unprocessable Content',           // RFC9110
		423 => 'Locked',                          // RFC4918
		424 => 'Failed Dependency',               // RFC4918
		425 => 'Too Early',                       // RFC8470
		426 => 'Upgrade Required',                // RFC9110, Section 15.5.22
		428 => 'Precondition Required',           // RFC6585
		429 => 'Too Many Requests',               // RFC6585
		431 => 'Request Header Fields Too Large', // RFC6585
		451 => 'Unavailable For Legal Reasons',   // RFC7725
		500 => 'Internal Server Error',           // RFC9110, Section 15.6.1
		501 => 'Not Implemented',                 // RFC9110, Section 15.6.2
		502 => 'Bad Gateway',                     // RFC9110, Section 15.6.3
		503 => 'Service Unavailable',             // RFC9110, Section 15.6.4
		504 => 'Gateway Timeout',                 // RFC9110, Section 15.6.5
		505 => 'HTTP Version Not Supported',      // RFC9110, Section 15.6.6
		506 => 'Variant Also Negotiates',         // RFC2295
		507 => 'Insufficient Storage',            // RFC4918
		508 => 'Loop Detected',                   // RFC5842
		510 => 'Not Extended',                    // RFC2774
		511 => 'Network Authentication Required', // RFC6585
	];

	/**
	 * Constructor
	 *
	 * @param StreamInterface $body
	 * @param int             $status_code
	 * @param string          $protocol_version
	 */
	private function __construct(StreamInterface $body, $status_code, $protocol_version) {
		$this->body = $body;
		$this->status_code = $status_code;
		$this->protocol_version = $protocol_version;
	}

	/**
	 * Gets the response status code.
	 *
	 * The status code is a 3-digit integer result code of the server's attempt
	 * to understand and satisfy the request.
	 *
	 * @return int Status code.
	 */
	public function getStatusCode() {
		return $this->status_code;
	}

	/**
	 * Return an instance with the specified status code and, optionally, reason phrase.
	 *
	 * If no reason phrase is specified, implementations MAY choose to default
	 * to the RFC 7231 or IANA recommended reason phrase for the response's
	 * status code.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * updated status and reason phrase.
	 *
	 * @link http://tools.ietf.org/html/rfc7231#section-6
	 * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 * @param int $code The 3-digit integer result code to set.
	 * @param string $reasonPhrase The reason phrase to use with the
	 *     provided status code; if none is provided, implementations MAY
	 *     use the defaults as suggested in the HTTP specification.
	 * @return static
	 * @throws \InvalidArgumentException For invalid status code arguments.
	 */
	public function withStatus($code, $reasonPhrase = '') {
		throw new Exception('not implemented');
	}

	/**
	 * Gets the response reason phrase associated with the status code.
	 *
	 * Because a reason phrase is not a required element in a response
	 * status line, the reason phrase value MAY be null. Implementations MAY
	 * choose to return the default RFC 7231 recommended reason phrase (or those
	 * listed in the IANA HTTP Status Code Registry) for the response's
	 * status code.
	 *
	 * @link http://tools.ietf.org/html/rfc7231#section-6
	 * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 * @return string Reason phrase; must return an empty string if none present.
	 */
	public function getReasonPhrase() {
		if (array_key_exists($this->status_code, $this->reasonPhrases)) {
			return $this->reasonPhrases[$this->status_code];
		}

		return '';
	}

	/**
	 * Retrieves the HTTP protocol version as a string.
	 *
	 * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
	 *
	 * @return string HTTP protocol version.
	 */
	public function getProtocolVersion() {
		return $this->protocol_version;
	}

	/**
	 * Return an instance with the specified HTTP protocol version.
	 *
	 * The version string MUST contain only the HTTP version number (e.g.,
	 * "1.1", "1.0").
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new protocol version.
	 *
	 * @param string $version HTTP protocol version
	 * @return static
	 */
	public function withProtocolVersion($version) {
		throw new Exception('not implemented');
	}

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
		throw new Exception('not implemented');
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
		throw new Exception('not implemented');
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
		throw new Exception('not implemented');
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
		throw new Exception('not implemented');
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
		throw new Exception('not implemented');
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
		throw new Exception('not implemented');
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
		throw new Exception('not implemented');
	}

	/**
	 * Gets the body of the message.
	 *
	 * @return StreamInterface Returns the body as a stream.
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * Return an instance with the specified message body.
	 *
	 * The body MUST be a StreamInterface object.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return a new instance that has the
	 * new body stream.
	 *
	 * @param StreamInterface $body Body.
	 * @return static
	 * @throws \InvalidArgumentException When the body is not valid.
	 */
	public function withBody(StreamInterface $body) {
		throw new Exception('not implemented');
	}
}
