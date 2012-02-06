<?php
/**
 * Requests for PHP
 *
 * Inspired by Requests for Python.
 *
 * Based on concepts from SimplePie_File, RequestCore and WP_Http.
 *
 * @package Requests
 */

/**
 * Requests for PHP
 *
 * Inspired by Requests for Python.
 *
 * Based on concepts from SimplePie_File, RequestCore and WP_Http.
 *
 * @todo Add non-blocking request support
 * @package Requests
 */
class Requests {
	/**
	 * POST method
	 */
	const POST = 'POST';

	/**
	 * GET method
	 */
	const GET = 'GET';

	/**
	 * HEAD method
	 */
	const HEAD = 'HEAD';

	/**
	 * Current version of Requests
	 */
	const VERSION = '1.5';

	/**
	 * Registered transport classes
	 * @var array
	 */
	protected static $transports = array();

	/**
	 * Selected transport name
	 *
	 * Use {@see get_transport()} instead
	 *
	 * @var string|null
	 */
	public static $transport = null;

	/**
	 * This is a static class, do not instantiate it
	 */
	private function __construct() {}

	/**
	 * Register a transport
	 *
	 * @param Requests_Transport $transport Transport to add, must support the Requests_Transport interface
	 */
	public static function add_transport($transport) {
		if (empty(self::$transports)) {
			self::$transports = array(
				'Requests_Transport_cURL',
				'Requests_Transport_fsockopen',
			);
		}
		self::$transports = array_merge(self::$transports, array($transport));
	}

	/**
	 * Get a working transport
	 *
	 * @return Requests_Transport
	 */
	protected static function get_transport() {
		if (!is_null(self::$transport)) {
			return new self::$transport();
		}
		if (empty(self::$transports)) {
			self::$transports = array(
				'Requests_Transport_cURL',
				'Requests_Transport_fsockopen',
			);
		}

		// Find us a working transport
		foreach (self::$transports as $class) {
			if (!class_exists($class))
				continue;

			$result = call_user_func(array($class, 'test'));
			if ($result) {
				self::$transport = $class;
				break;
			}
		}
		if (self::$transport === null) {
			throw new Requests_Exception('No working transports found', 'notransport', self::$transports);
		}

		return new self::$transport();
	}

	/**#@+
	 * Convienience function
	 *
	 * @see request()
	 * @param string $url
	 * @param array $headers
	 * @param array $data
	 * @param array $options
	 * @return Requests_Response
	 */
	public static function get($url, $headers = array(), $options = array()) {
		return self::request($url, $headers, null, self::GET, $options);
	}
	public static function head($url, $headers = array(), $options = array()) {
		return self::request($url, $headers, null, self::HEAD, $options);
	}
	public static function post($url, $headers = array(), $data = array(), $options = array()) {
		return self::request($url, $headers, $data, self::POST, $options);
	}
	/**#@-*/

	/**
	 * Main interface for HTTP requests
	 *
	 * @param string $url URL to request
	 * @param array $headers Extra headers to send with the request
	 * @param array $data Data to send either as a query string for GET/HEAD requests, or in the body for POST requests
	 * @param string $type HTTP request type (use Requests constants)
	 * @return Requests_Response
	 */
	public static function request($url, $headers = array(), $data = array(), $type = self::GET, $options = array()) {
		if (!preg_match('/^http(s)?:\/\//i', $url)) {
			throw new Requests_Exception('Only HTTP requests are handled.', 'nonhttp', $url);
		}
		$defaults = array(
			'timeout' => 10,
			'useragent' => 'php-requests/' . self::VERSION,
			'redirected' => 0,
			'redirects' => 10,
			'follow_redirects' => true,
			'blocking' => true,
			'type' => $type,
			'filename' => false,
			'auth' => false,
			'idn' => true,
		);
		$options = array_merge($defaults, $options);

		// Special case for simple basic auth
		if (is_array($options['auth'])) {
			$options['auth'] = new Requests_Auth_Basic($options['auth']);
		}
		if ($options['auth'] !== false) {
			$options['auth']->before_request($url, $headers, $data, $type, $options);
		}

		if ($options['idn'] !== false) {
			$iri = new Requests_IRI($url);
			$iri->ihost = Requests_IDNAEncoder::encode($iri->ihost);
			$url = (string) $iri;
		}

		$transport = self::get_transport();
		$response = $transport->request($url, $headers, $data, $options);
		return self::parse_response($response, $url, $headers, $data, $options);
	}

	/**
	 * HTTP response parser
	 *
	 * @param string $headers Full response text including headers and body
	 * @param string $url Original request URL
	 * @param array $req_headers Original $headers array passed to {@link request()}, in case we need to follow redirects
	 * @param array $req_data Original $data array passed to {@link request()}, in case we need to follow redirects
	 * @param array $options Original $options array passed to {@link request()}, in case we need to follow redirects
	 * @return Requests_Response
	 */
	protected static function parse_response($headers, $url, $req_headers, $req_data, $options) {
		$return = new Requests_Response();
		if (!$options['blocking']) {
			return $return;
		}

		$return->url = $url;

		if (!$options['filename']) {
			$headers = explode("\r\n\r\n", $headers, 2);
			$return->body = array_pop($headers);
			$headers = $headers[0];
		}
		else {
			$return->body = '';
		}
		// Pretend CRLF = LF for compatibility (RFC 2616, section 19.3)
		$headers = str_replace("\r\n", "\n", $headers);
		// Unfold headers (replace [CRLF] 1*( SP | HT ) with SP) as per RFC 2616 (section 2.2)
		$headers = preg_replace('/\n[ \t]/', ' ', $headers);
		$headers = explode("\n", $headers);
		preg_match('#^HTTP/1\.\d[ \t]+(\d+)#i', array_shift($headers), $matches);
		if (empty($matches)) {
			throw new Requests_Exception('Response could not be parsed', 'noversion', $headers);
		}
		$return->status_code = (int) $matches[1];
		if ($return->status_code >= 200 && $return->status_code < 300) {
			$return->success = true;
		}

		foreach ($headers as $header) {
			list($key, $value) = explode(':', $header, 2);
			$value = trim($value);
			preg_replace('#(\s+)#i', ' ', $value);
			if (isset($return->headers[$key])) {
				// RFC2616 notes that multiple headers must be able to
				// be combined like this. We should use a smarter way though.
				$return->headers[$key] .= ',' . $value;
			}
			else {
				$return->headers[$key] = $value;
			}
		}
		if (isset($return->headers['transfer-encoding'])) {
			$return->body = self::decode_chunked($return->body);
			unset($return->headers['transfer-encoding']);
		}
		if (isset($return->headers['content-encoding'])) {
			$return->body = self::decompress($return->body);
		}

		//fsockopen and cURL compatibility
		if (isset($return->headers['connection'])) {
			unset($return->headers['connection']);
		}

		if ((in_array($return->status_code, array(300, 301, 302, 303, 307)) || $return->status_code > 307 && $return->status_code < 400) && $options['follow_redirects'] === true) {
			if (isset($return->headers['location']) && $options['redirected'] < $options['redirects']) {
				$options['redirected']++;
				$location = $return->headers['location'];
				$redirected = self::request($location, $req_headers, $req_data, false, $options);
				$redirected->history[] = $return;
				return $redirected;
			}
			elseif ($options['redirected'] >= $options['redirects']) {
				throw new Requests_Exception('Too many redirects', 'toomanyredirects', $return);
			}
		}

		$return->redirects = $options['redirected'];
		return $return;
	}

	protected static function decode_chunked($data) {
		if (!preg_match('/^[0-9a-f]+(\s|\r|\n)+/mi', trim($data))) {
			return $data;
		}

		$decoded = '';
		$body = ltrim($data, "\r\n");

		while (true) {
			$is_chunked = (bool) preg_match( '/^([0-9a-f]+)(\s|\r|\n)+/i', $body, $matches );
			if (!$is_chunked) {
				// Looks like it's not chunked after all
				//throw new Exception('Not chunked after all: ' . $body);\
				return $body;
			}

			$length = hexdec($matches[1]);
			$chunk_length = strlen($matches[0]);
			$decoded .= $part = substr($body, $chunk_length, $length);
			$body = ltrim(substr($body, $chunk_length), "\r\n");

			if (trim($body) === '0') {
				// We'll just ignore the footer headers
				return $decoded;
			}
		}
	}

	public static function flattern($array) {
		$return = array();
		foreach ($array as $key => $value) {
			$return[] = "$key: $value";
		}
		return $return;
	}

	protected static function decompress($data) {
		if (substr($data, 0, 2) !== "\x1f\x8b") {
			// Not actually compressed. Probably cURL ruining this for us.
			return $data;
		}

		if (function_exists('gzdecode') && ($decoded = gzdecode($data)) !== false) {
			return $decoded;
		}
		elseif (function_exists('gzinflate') && ($decoded = @gzinflate($data)) !== false) {
			return $decoded;
		}
		elseif (($decoded = self::compatible_gzinflate($data)) !== false) {
			return $decoded;
		}
		elseif (function_exists('gzuncompress') && ($decoded = @gzuncompress($data)) !== false) {
			return $decoded;
		}

		return $data;
	}

	/**
	 * Decompress deflated string while staying compatible with the majority of servers.
	 *
	 * Certain servers will return deflated data with headers which PHP's gziniflate()
	 * function cannot handle out of the box. The following function lifted from
	 * http://au2.php.net/manual/en/function.gzinflate.php#77336 will attempt to deflate
	 * the various return forms used.
	 *
	 * @link http://au2.php.net/manual/en/function.gzinflate.php#77336
	 *
	 * @param string $gzData String to decompress.
	 * @return string|bool False on failure.
	 */
	protected static function compatible_gzinflate($gzData) {
		if ( substr($gzData, 0, 3) == "\x1f\x8b\x08" ) {
			$i = 10;
			$flg = ord( substr($gzData, 3, 1) );
			if ( $flg > 0 ) {
				if ( $flg & 4 ) {
					list($xlen) = unpack('v', substr($gzData, $i, 2) );
					$i = $i + 2 + $xlen;
				}
				if ( $flg & 8 )
					$i = strpos($gzData, "\0", $i) + 1;
				if ( $flg & 16 )
					$i = strpos($gzData, "\0", $i) + 1;
				if ( $flg & 2 )
					$i = $i + 2;
			}
			return gzinflate( substr($gzData, $i, -8) );
		} else {
			return false;
		}
	}
}