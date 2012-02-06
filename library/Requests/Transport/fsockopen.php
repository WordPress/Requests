<?php
/**
 * fsockopen HTTP transport
 *
 * @package Requests
 */

/**
 * fsockopen HTTP transport
 *
 * @package Requests
 */
class Requests_Transport_fsockopen implements Requests_Transport {
	public $headers = array();
	public $info;

	public function request($url, $headers = array(), $data = array(), $options = array()) {
		$options['hooks']->dispatch('fsockopen.before_request');

		$url_parts = parse_url($url);
		if (isset($url_parts['scheme']) && strtolower($url_parts['scheme']) === 'https') {
			$url_parts['host'] = "ssl://$url_parts[host]";
			$url_parts['port'] = 443;
		}
		if (!isset($url_parts['port'])) {
			$url_parts['port'] = 80;
		}
		$fp = @fsockopen($url_parts['host'], $url_parts['port'], $errno, $errstr, $options['timeout']);
		if (!$fp) {
			throw new Requests_Exception($errstr, 'fsockopenerror');
			return;
		}
		stream_set_timeout($fp, $options['timeout']);

		$request_body = '';
		$out = '';
		switch ($options['type']) {
			case Requests::POST:
				if (isset($url_parts['path'])) {
					$path = $url_parts['path'];
					if (isset($url_parts['query'])) {
						$path .= '?' . $url_parts['query'];
					}
				}
				else {
					$path = '/';
				}
				$out = "POST $path HTTP/1.0\r\n";
				if (is_array($data)) {
					$request_body = http_build_query($data, null, '&');
				}
				else {
					$request_body = $data;
				}
				$headers['Content-Length'] = strlen($request_body);
				$headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
				break;
			case Requests::HEAD:
				$head = self::format_get($url_parts, $data);
				$out = "HEAD $head HTTP/1.0\r\n";
				break;
			default:
				$get = self::format_get($url_parts, $data);
				$out = "GET $get HTTP/1.0\r\n";
				break;
		}
		$out .= "Host: {$url_parts['host']}\r\n";
		$out .= "User-Agent: {$options['useragent']}\r\n";
		$accept_encoding = $this->accept_encoding();
		if (!empty($accept_encoding)) {
			$out .= "Accept-Encoding: $accept_encoding\r\n";
		}

		if (isset($url_parts['user']) && isset($url_parts['pass'])) {
			$out .= "Authorization: Basic " . base64_encode("$url_parts[user]:$url_parts[pass]") . "\r\n";
		}

		$headers = Requests::flattern($headers);
		$out .= implode($headers, "\r\n");

		$options['hooks']->dispatch('fsockopen.after_headers', array(&$out));

		$out .= "\r\nConnection: Close\r\n\r\n" . $request_body;

		$options['hooks']->dispatch('fsockopen.before_send', array(&$out));

		fwrite($fp, $out);
		$options['hooks']->dispatch('fsockopen.after_send', array(&$fake_headers));

		if (!$options['blocking']) {
			fclose($fp);
			$fake_headers = '';
			$options['hooks']->dispatch('fsockopen.after_request', array(&$fake_headers));
			return '';
		}

		$this->info = stream_get_meta_data($fp);

		$this->headers = '';
		$this->info = stream_get_meta_data($fp);
		if (!$options['filename']) {
			while (!feof($fp)) {
				$this->headers .= fread($fp, 1160);
			}
		}
		else {
			$download = fopen($options['filename'], 'wb');
			$doingbody = false;
			$response = '';
			while (!feof($fp)) {
				$block = fread($fp, 1160);
				if ($doingbody) {
					fwrite($download, $block);
				}
				else {
					$response .= $block;
					if (strpos($response, "\r\n\r\n")) {
						list($this->headers, ) = explode("\r\n\r\n", $response, 2);
						$doingbody = true;
					}
				}
			}
			fclose($download);
		}
		if ($this->info['timed_out']) {
			throw new Requests_Exception('fsocket timed out', 'timeout');
		}
		fclose($fp);

		$options['hooks']->dispatch('fsockopen.after_request', array(&$this->headers));
		return $this->headers;
	}

	protected static function accept_encoding() {
		$type = array();
		if (function_exists('gzinflate')) {
			$type[] = 'deflate;q=1.0';
		}

		if (function_exists('gzuncompress')) {
			$type[] = 'compress;q=0.5';
		}

		$type[] = 'gzip;q=0.5';

		return implode(', ', $type);
	}

	protected static function format_get($url_parts, $data) {
		if (!empty($data)) {
			if (empty($url_parts['query']))
				$url_parts['query'] = '';

			$url_parts['query'] .= '&' . http_build_query($data, null, '&');
			$url_parts['query'] = trim($url_parts['query'], '&');
		}
		if (isset($url_parts['path'])) {
			if (isset($url_parts['query'])) {
				$get = "$url_parts[path]?$url_parts[query]";
			}
			else {
				$get = $url_parts['path'];
			}
		}
		else {
			$get = '/';
		}
		return $get;
	}

	/**
	 * Whether this transport is valid
	 *
	 * @return boolean True if the transport is valid, false otherwise.
	 */
	public static function test() {
		return function_exists('fsockopen');
	}
}