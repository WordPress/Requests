<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Utility;

use WpOrg\Requests\Exception\InvalidArgument;

/**
 * Helper class for dealing with HTTP status codes.
 *
 * The codes are synced with the mdn web docs at https://developer.mozilla.org/en-US/docs/Web/HTTP/Status.
 *
 * @package Requests\Utilities
 * @since   2.1.0
 */
final class HttpStatus {

	// Informational responses (100 - 199)

	/**
	 * HTTP response code 100 - Continue.
	 *
	 * This interim response indicates that the client should continue the request or ignore the response if the request
	 * is already finished.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/100
	 */
	const TEXT_100 = 'Continue';

	/**
	 * HTTP response code 101 - Switching Protocols.
	 *
	 * This code is sent in response to an Upgrade request header from the client, and indicates the protocol the server
	 * is switching to.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/101
	 */
	const TEXT_101 = 'Switching Protocols';

	/**
	 * HTTP response code 102 - Processing (WebDAV).
	 *
	 * This code indicates that the server has received and is processing the request, but no response is available yet.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/102
	 */
	const TEXT_102 = 'Processing';

	/**
	 * HTTP response code 103 - Early Hints.
	 *
	 * This status code is primarily intended to be used with the Link header, letting the user agent start preloading
	 * resources while the server prepares a response or preconnects to an origin from which the page will need
	 * resources.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/103
	 */
	const TEXT_103 = 'Early Hints';

	// Successful responses (200 - 299)

	/**
	 * HTTP response code 200 - OK.
	 *
	 * The request has succeeded.
	 *
	 * The result meaning of "success" depends on the HTTP method:
	 * - GET: The resource has been fetched and transmitted in the message body.
	 * - HEAD: The representation headers are included in the response without any message body.
	 * - PUT or POST: The resource describing the result of the action is transmitted in the message body.
	 * - TRACE: The message body contains the request message as received by the server.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200
	 */
	const TEXT_200 = 'OK';

	/**
	 * HTTP response code 201 - Created.
	 *
	 * The request has succeeded and a new resource has been created as a result.
	 *
	 * This is typically the response sent after POST requests, or some PUT requests.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/201
	 */
	const TEXT_201 = 'Created';

	/**
	 * HTTP response code 202 - Accepted.
	 *
	 * The request has been received but not yet acted upon.
	 *
	 * It is noncommittal, since there is no way in HTTP to later send an asynchronous response indicating the outcome
	 * of the request. It is intended for cases where another process or server handles the request, or for batch
	 * processing.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/202
	 */
	const TEXT_202 = 'Accepted';

	/**
	 * HTTP response code 203 - Non-Authoritative Information.
	 *
	 * This response code means the returned metadata is not exactly the same as is available from the origin server,
	 * but is collected from a local or a third-party copy.
	 *
	 * This is mostly used for mirrors or backups of another resource. Except for that specific case, the 200 OK
	 * response is preferred to this status.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/203
	 */
	const TEXT_203 = 'Non-Authoritative Information';

	/**
	 * HTTP response code 204 - No Content.
	 *
	 * There is no content to send for this request, but the headers may be useful.
	 *
	 * The user-agent may update its cached headers for this resource with the new ones.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/204
	 */
	const TEXT_204 = 'No Content';

	/**
	 * HTTP response code 205 - Reset Content.
	 *
	 * Tells the user-agent to reset the document which sent this request.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/205
	 */
	const TEXT_205 = 'Reset Content';

	/**
	 * HTTP response code 206 - Partial Content.
	 *
	 * This response code is used when the Range header is sent from the client to request only part of a resource.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/206
	 */
	const TEXT_206 = 'Partial Content';

	/**
	 * HTTP response code 207 - Multi-Status (WebDAV).
	 *
	 * Conveys information about multiple resources, for situations where multiple status codes might be appropriate.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/207
	 */
	const TEXT_207 = 'Multi-Status';

	/**
	 * HTTP response code 208 - Already Reported (WebDAV).
	 *
	 * Used inside a <dav:propstat> response element to avoid enumerating the internal members of multiple bindings to
	 * the same collection.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/208
	 */
	const TEXT_208 = 'Already Reported';

	/**
	 * HTTP response code 226 - IM Used (HTTP Delta encoding).
	 *
	 * This is mostly used for mirrors or backups of another resource. Except for that specific case, the 200 OK
	 * response is preferred to this status.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/226
	 */
	const TEXT_226 = 'IM Used';

	// Redirection messages (300 - 399)

	/**
	 * HTTP response code 300 - Multiple Choices.
	 *
	 * The request has more than one possible response.
	 *
	 * The user agent or user should choose one of them. (There is no standardized way of choosing one of the responses,
	 * but HTML links to the possibilities are recommended so the user can pick.)
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/300
	 */
	const TEXT_300 = 'Multiple Choices';

	/**
	 * HTTP response code 301 - Moved Permanently.
	 *
	 * The URL of the requested resource has been changed permanently.
	 *
	 * The new URL is given in the response.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/301
	 */
	const TEXT_301 = 'Moved Permanently';

	/**
	 * HTTP response code 302 - Found.
	 *
	 * This response code means that the URI of requested resource has been changed temporarily.
	 *
	 * Further changes in the URI might be made in the future. Therefore, this same URI should be used by the client in
	 * future requests.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/302
	 */
	const TEXT_302 = 'Found';

	/**
	 * HTTP response code 303 - See Other.
	 *
	 * The server sent this response to direct the client to get the requested resource at another URI with a GET
	 * request.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
	 */
	const TEXT_303 = 'See Other';

	/**
	 * HTTP response code 304 - Not Modified.
	 *
	 * This is used for caching purposes. It tells the client that the response has not been modified, so the client can
	 * continue to use the same cached version of the response.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/304
	 */
	const TEXT_304 = 'Not Modified';

	/**
	 * HTTP response code 305 - Use Proxy (DEPRECATED!).
	 *
	 * Was defined in a previous version of the HTTP specification to indicate that a requested response must be
	 * accessed by a proxy.
	 *
	 * It has been deprecated due to security concerns regarding in-band configuration of a proxy.
	 *
	 * @deprecated
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/305
	 */
	const TEXT_305 = 'Use Proxy';

	/**
	 * HTTP response code 306 - Switch Proxy (DEPRECATED!).
	 *
	 * No longer used. Originally meant "Subsequent requests should use the specified proxy."
	 *
	 * @deprecated
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/306
	 */
	const TEXT_306 = 'Switch Proxy';

	/**
	 * HTTP response code 307 - Temporary Redirect.
	 *
	 * The server sends this response to direct the client to get the requested resource at another URI with same method
	 * that was used in the prior request.
	 *
	 * This has the same semantics as the 302 Found HTTP response code, with the exception that the user agent must not
	 * change the HTTP method used: If a POST was used in the first request, a POST must be used in the second request.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/307
	 */
	const TEXT_307 = 'Temporary Redirect';

	/**
	 * HTTP response code 308 - Permanent Redirect.
	 *
	 * This means that the resource is now permanently located at another URI, specified by the Location: HTTP Response
	 * header.
	 *
	 * This has the same semantics as the 301 Moved Permanently HTTP response code, with the exception that the user
	 * agent must not change the HTTP method used: If a POST was used in the first request, a POST must be used in the
	 * second request.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/308
	 */
	const TEXT_308 = 'Permanent Redirect';

	// Client error responses (400 - 499)

	/**
	 * HTTP response code 400 - Bad Request.
	 *
	 * The server cannot or will not process the request due to something that is perceived to be a client error (e.g.,
	 * malformed request syntax, invalid request message framing, or deceptive request routing).
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/400
	 */
	const TEXT_400 = 'Bad Request';

	/**
	 * HTTP response code 401 - Unauthorized.
	 *
	 * Although the HTTP standard specifies "unauthorized", semantically this response means "unauthenticated". That is,
	 * the client must authenticate itself to get the requested response.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/401
	 */
	const TEXT_401 = 'Unauthorized';

	/**
	 * HTTP response code 402 - Payment Required (Experimental).
	 *
	 * This response code is reserved for future use. The initial aim for creating this code was using it for digital
	 * payment systems, however this status code is used very rarely and no standard convention exists.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/402
	 */
	const TEXT_402 = 'Payment Required';

	/**
	 * HTTP response code 403 - Forbidden.
	 *
	 * The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to
	 * give the requested resource.
	 *
	 * Unlike 401 Unauthorized, the client's identity is known to the server.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403
	 */
	const TEXT_403 = 'Forbidden';

	/**
	 * HTTP response code 404 - Not Found.
	 *
	 * The server cannot find the requested resource.
	 *
	 * In the browser, this means the URL is not recognized.
	 *
	 * In an API, this can also mean that the endpoint is valid but the resource itself does not exist. Servers may also
	 * send this response instead of 403 to hide the existence of a resource from an unauthorized client.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404
	 */
	const TEXT_404 = 'Not Found';

	/**
	 * HTTP response code 405 - Method Not Allowed.
	 *
	 * The request method is known by the server but is not supported by the target resource. For example, an API may
	 * not allow calling DELETE to remove a resource.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/405
	 */
	const TEXT_405 = 'Method Not Allowed';

	/**
	 * HTTP response code 406 - Not Acceptable.
	 *
	 * This response is sent when the web server, after performing server-driven content negotiation, doesn't find any
	 * content that conforms to the criteria given by the user agent.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/406
	 */
	const TEXT_406 = 'Not Acceptable';

	/**
	 * HTTP response code 407 - Proxy Authentication Required.
	 *
	 * This is similar to 401 but authentication is needed to be done by a proxy.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/407
	 */
	const TEXT_407 = 'Proxy Authentication Required';

	/**
	 * HTTP response code 408 - Request Timeout.
	 *
	 * This response is sent on an idle connection by some servers, even without any previous request by the client.
	 *
	 * It means that the server would like to shut down this unused connection. This response is used much more since
	 * some browsers, like Chrome, Firefox 27+, or IE9, use HTTP pre-connection mechanisms to speed up surfing.
	 *
	 * Also note that some servers merely shut down the connection without sending this message.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/408
	 */
	const TEXT_408 = 'Request Timeout';

	/**
	 * HTTP response code 409 - Conflict.
	 *
	 * This response is sent when a request conflicts with the current state of the server.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/409
	 */
	const TEXT_409 = 'Conflict';

	/**
	 * HTTP response code 410 - Gone.
	 *
	 * This response is sent when the requested content has been permanently deleted from server, with no forwarding
	 * address.
	 *
	 * Clients are expected to remove their caches and links to the resource.
	 * The HTTP specification intends this status code to be used for "limited-time, promotional services".
	 * APIs should not feel compelled to indicate resources that have been deleted with this status code.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/410
	 */
	const TEXT_410 = 'Gone';

	/**
	 * HTTP response code 411 - Length Required.
	 *
	 * Server rejected the request because the Content-Length header field is not defined and the server requires it.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/411
	 */
	const TEXT_411 = 'Length Required';

	/**
	 * HTTP response code 412 - Precondition Failed.
	 *
	 * The client has indicated preconditions in its headers which the server does not meet.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/412
	 */
	const TEXT_412 = 'Precondition Failed';

	/**
	 * HTTP response code 413 - Payload Too Large.
	 *
	 * Request entity is larger than limits defined by server; the server might close the connection or return an
	 * Retry-After header field.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/413
	 */
	const TEXT_413 = 'Payload Too Large';

	/**
	 * HTTP response code 414 - URI Too Long.
	 *
	 * The URI requested by the client is longer than the server is willing to interpret.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/414
	 */
	const TEXT_414 = 'URI Too Long';

	/**
	 * HTTP response code 415 - Unsupported Media Type.
	 *
	 * The media format of the requested data is not supported by the server, so the server is rejecting the request.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/415
	 */
	const TEXT_415 = 'Unsupported Media Type';

	/**
	 * HTTP response code 416 - Range Not Satisfiable.
	 *
	 * The range specified by the Range header field in the request can't be fulfilled; it's possible that the range is
	 * outside the size of the target URI's data.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/416
	 */
	const TEXT_416 = 'Requested Range Not Satisfiable';

	/**
	 * HTTP response code 417 - Expectation Failed.
	 *
	 * This response code means the expectation indicated by the Expect request header field can't be met by the server.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/417
	 */
	const TEXT_417 = 'Expectation Failed';

	/**
	 * HTTP response code 418 - I'm a teapot.
	 *
	 * The server refuses the attempt to brew coffee with a teapot.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/418
	 */
	const TEXT_418 = "I'm a teapot";

	/**
	 * HTTP response code 421 - Misdirected Request.
	 *
	 * The request was directed at a server that is not able to produce a response.
	 *
	 * This can be sent by a server that is not configured to produce responses for the combination of scheme and
	 * authority that are included in the request URI.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/421
	 */
	const TEXT_421 = 'Misdirected Request';

	/**
	 * HTTP response code 422 - Unprocessable Entity (WebDAV).
	 *
	 * The request was well-formed but was unable to be followed due to semantic errors.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/422
	 */
	const TEXT_422 = 'Unprocessable Content';

	/**
	 * HTTP response code 423 - Locked (WebDAV).
	 *
	 * The resource that is being accessed is locked.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/423
	 */
	const TEXT_423 = 'Locked';

	/**
	 * HTTP response code 424 - Failed Dependency (WebDAV).
	 *
	 * The request failed due to failure of a previous request.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/424
	 */
	const TEXT_424 = 'Failed Dependency';

	/**
	 * HTTP response code 425 - Too Early (Experimental).
	 *
	 * Indicates that the server is unwilling to risk processing a request that might be replayed.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/425
	 */
	const TEXT_425 = 'Too Early';

	/**
	 * HTTP response code 426 - Upgrade Required.
	 *
	 * The server refuses to perform the request using the current protocol but might be willing to do so after the
	 * client upgrades to a different protocol.
	 *
	 * The server sends an Upgrade header in a 426 response to indicate the required protocol(s).
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/426
	 */
	const TEXT_426 = 'Upgrade Required';

	/**
	 * HTTP response code 428 - Precondition Required.
	 *
	 * The origin server requires the request to be conditional.
	 *
	 * Intended to prevent the 'lost update' problem, where a client GETs a resource's state, modifies it, and PUTs it
	 * back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/428
	 */
	const TEXT_428 = 'Precondition Required';

	/**
	 * HTTP response code 429 - Too Many Requests.
	 *
	 * The user has sent too many requests in a given amount of time ("rate limiting").
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429
	 */
	const TEXT_429 = 'Too Many Requests';

	/**
	 * HTTP response code 431 - Request Header Fields Too Large.
	 *
	 * The server is unwilling to process the request because its header fields are too large.
	 *
	 * The request may be resubmitted after reducing the size of the request header fields.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/431
	 */
	const TEXT_431 = 'Request Header Fields Too Large';

	/**
	 * HTTP response code 451 - Unavailable For Legal Reasons.
	 *
	 * The user-agent requested a resource that cannot legally be provided, such as a web page censored by a government.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/451
	 */
	const TEXT_451 = 'Unavailable For Legal Reasons';

	// Server error responses (500 - 599)

	/**
	 * HTTP response code 500 - Internal Server Error.
	 *
	 * The server has encountered a situation it doesn't know how to handle.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500
	 */
	const TEXT_500 = 'Internal Server Error';

	/**
	 * HTTP response code 501 - Not Implemented.
	 *
	 * The request method is not supported by the server and cannot be handled.
	 *
	 * The only methods that servers are required to support (and therefore that must not return this code) are GET and
	 * HEAD.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/501
	 */
	const TEXT_501 = 'Not Implemented';

	/**
	 * HTTP response code 502 - Bad Gateway.
	 *
	 * This error response means that the server, while working as a gateway to get a response needed to handle the
	 * request, got an invalid response.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/502
	 */
	const TEXT_502 = 'Bad Gateway';

	/**
	 * HTTP response code 503 - Service Unavailable.
	 *
	 * The server is not ready to handle the request.
	 *
	 * Common causes are a server that is down for maintenance or that is overloaded.
	 *
	 * Note that together with this response, a user-friendly page explaining the problem should be sent.
	 *
	 * This responses should be used for temporary conditions and the Retry-After: HTTP header should, if possible,
	 * contain the estimated time for the recovery of the service. The webmaster must also take care about the
	 * caching-related headers that are sent along with this response, as these temporary condition responses should
	 * usually not be cached.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/503
	 */
	const TEXT_503 = 'Service Unavailable';

	/**
	 * HTTP response code 504 - Gateway Timeout.
	 *
	 * This error response is given when the server is acting as a gateway and cannot get a response in time.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/504
	 */
	const TEXT_504 = 'Gateway Timeout';

	/**
	 * HTTP response code 505 - HTTP Version Not Supported.
	 *
	 * The HTTP version used in the request is not supported by the server.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/505
	 */
	const TEXT_505 = 'HTTP Version Not Supported';

	/**
	 * HTTP response code 506 - Variant Also Negotiates.
	 *
	 * The server has an internal configuration error: the chosen variant resource is configured to engage in
	 * transparent content negotiation itself, and is therefore not a proper end point in the negotiation process.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/506
	 */
	const TEXT_506 = 'Variant Also Negotiates';

	/**
	 * HTTP response code 507 - Insufficient Storage (WebDAV).
	 *
	 * The method could not be performed on the resource because the server is unable to store the representation needed
	 * to successfully complete the request.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/507
	 */
	const TEXT_507 = 'Insufficient Storage';

	/**
	 * HTTP response code 508 - Loop Detected (WebDAV).
	 *
	 * The server detected an infinite loop while processing the request.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/508
	 */
	const TEXT_508 = 'Loop Detected';

	/**
	 * HTTP response code 510 - Not Extended.
	 *
	 * Further extensions to the request are required for the server to fulfill it.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/510
	 */
	const TEXT_510 = 'Not Extended';

	/**
	 * HTTP response code 511 - Network Authentication Required.
	 *
	 * The 511 status code indicates that the client needs to authenticate to gain network access.
	 *
	 * @var string
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/511
	 */
	const TEXT_511 = 'Network Authentication Required';


	/**
	 * Map of status codes to their text.
	 *
	 * @var array<string>
	 */
	const MAP = [
		100 => self::TEXT_100,
		101 => self::TEXT_101,
		102 => self::TEXT_102,
		103 => self::TEXT_103,
		200 => self::TEXT_200,
		201 => self::TEXT_201,
		202 => self::TEXT_202,
		203 => self::TEXT_203,
		204 => self::TEXT_204,
		205 => self::TEXT_205,
		206 => self::TEXT_206,
		207 => self::TEXT_207,
		208 => self::TEXT_208,
		226 => self::TEXT_226,
		300 => self::TEXT_300,
		301 => self::TEXT_301,
		302 => self::TEXT_302,
		303 => self::TEXT_303,
		304 => self::TEXT_304,
		305 => self::TEXT_305,
		306 => self::TEXT_306,
		307 => self::TEXT_307,
		308 => self::TEXT_308,
		400 => self::TEXT_400,
		401 => self::TEXT_401,
		402 => self::TEXT_402,
		403 => self::TEXT_403,
		404 => self::TEXT_404,
		405 => self::TEXT_405,
		406 => self::TEXT_406,
		407 => self::TEXT_407,
		408 => self::TEXT_408,
		409 => self::TEXT_409,
		410 => self::TEXT_410,
		411 => self::TEXT_411,
		412 => self::TEXT_412,
		413 => self::TEXT_413,
		414 => self::TEXT_414,
		415 => self::TEXT_415,
		416 => self::TEXT_416,
		417 => self::TEXT_417,
		418 => self::TEXT_418,
		421 => self::TEXT_421,
		422 => self::TEXT_422,
		423 => self::TEXT_423,
		424 => self::TEXT_424,
		425 => self::TEXT_425,
		426 => self::TEXT_426,
		428 => self::TEXT_428,
		429 => self::TEXT_429,
		431 => self::TEXT_431,
		451 => self::TEXT_451,
		500 => self::TEXT_500,
		501 => self::TEXT_501,
		502 => self::TEXT_502,
		503 => self::TEXT_503,
		504 => self::TEXT_504,
		505 => self::TEXT_505,
		506 => self::TEXT_506,
		507 => self::TEXT_507,
		508 => self::TEXT_508,
		510 => self::TEXT_510,
		511 => self::TEXT_511,
	];

	/**
	 * Get the status message from a status code.
	 *
	 * @param int|string $code Status code.
	 * @return string Status message.
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $code argument is not a valid status code.
	 */
	public static function get_text($code) {
		if (self::is_valid_code($code) === false) {
			// When the type is correct, add the value to the error message to help debugging.
			$type = gettype($code) . (is_scalar($code) ? " ($code)" : '');

			throw InvalidArgument::create(1, '$code', 'a valid HTTP status code as an int or numeric string', $type);
		}

		return self::MAP[$code];
	}

	/**
	 * Verify whether a status code is valid.
	 *
	 * @param int|string $code Status code to check.
	 * @return bool Whether the status code is valid.
	 */
	public static function is_valid_code($code) {
		if (!is_int($code) && !is_string($code)) {
			return false;
		}

		return array_key_exists($code, self::MAP);
	}
}
