<?php
/**
 * cURL HTTP transport
 *
 * @package Requests
 */

/**
 * cURL HTTP transport
 *
 * @package Requests
 */
class Requests_Transport_cURL implements Requests_Transport {
	public $headers = '';
	public $info;

	public function __construct() {
		$curl = curl_version();
		$this->version = $curl['version'];
		$this->fp = curl_init();

		curl_setopt($this->fp, CURLOPT_HEADER, false);
		curl_setopt($this->fp, CURLOPT_RETURNTRANSFER, 1);
		if (version_compare($this->version, '7.10.5', '>=')) {
			curl_setopt($this->fp, CURLOPT_ENCODING, '');
		}
		curl_setopt ($this->fp, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($this->fp, CURLOPT_SSL_VERIFYPEER, 0);
	}

	public function request($url, $headers = array(), $data = array(), $options = array()) {
		$options['hooks']->dispatch('curl.before_request', array(&$this->fp));

		$headers = Requests::flattern($headers);
		if (($options['type'] === Requests::HEAD || $options['type'] === Requests::GET) & !empty($data)) {
			$url = self::format_get($url, $data);
		}

		switch ($options['type']) {
			case Requests::POST:
				curl_setopt($this->fp, CURLOPT_POST, true);
				curl_setopt($this->fp, CURLOPT_POSTFIELDS, $data);
				break;
			case Requests::HEAD:
				curl_setopt($this->fp, CURLOPT_NOBODY, true);
				break;
		}

		curl_setopt($this->fp, CURLOPT_URL, $url);
		curl_setopt($this->fp, CURLOPT_TIMEOUT, $options['timeout']);
		curl_setopt($this->fp, CURLOPT_CONNECTTIMEOUT, $options['timeout']);
		curl_setopt($this->fp, CURLOPT_REFERER, $url);
		curl_setopt($this->fp, CURLOPT_USERAGENT, $options['useragent']);
		curl_setopt($this->fp, CURLOPT_HTTPHEADER, $headers);

		if (true === $options['blocking']) {
			curl_setopt($this->fp, CURLOPT_HEADERFUNCTION, array(&$this, 'stream_headers'));
		}

		$options['hooks']->dispatch('curl.before_send', array(&$this->fp));

		if ($options['filename'] !== false) {
			$stream_handle = fopen($options['filename'], 'wb');
			curl_setopt($this->fp, CURLOPT_FILE, $stream_handle);
		}

		$response = curl_exec($this->fp);

		$options['hooks']->dispatch('curl.after_send', array(&$fake_headers));

		if ($options['blocking'] === false) {
			curl_close($this->fp);
			$fake_headers = '';
			$options['hooks']->dispatch('curl.after_request', array(&$fake_headers));
			return false;
		}
		if ($options['filename'] !== false) {
			fclose($stream_handle);
			$this->headers = trim($this->headers);
		}
		else {
			$this->headers .= $response;
		}

		if (curl_errno($this->fp) === 23 || curl_errno($this->fp) === 61) {
			curl_setopt($this->fp, CURLOPT_ENCODING, 'none');
			$this->headers = curl_exec($this->fp);
		}
		if (curl_errno($this->fp)) {
			throw new Requests_Exception('cURL error ' . curl_errno($this->fp) . ': ' . curl_error($this->fp), 'curlerror', $this->fp);
			return;
		}
		$this->info = curl_getinfo($this->fp);
		curl_close($this->fp);

		$options['hooks']->dispatch('curl.after_request', array(&$this->headers));
		return $this->headers;
	}

	protected function stream_headers($handle, $headers) {
		$this->headers .= $headers;
		return strlen($headers);
	}

	protected static function format_get($url, $data) {
		if (!empty($data)) {
			$url_parts = parse_url($url);
			if (empty($url_parts['query'])) {
				$query = $url_parts['query'] = '';
			}
			else {
				$query = $url_parts['query'];
			}

			$query .= '&' . http_build_query($data, null, '&');
			$query = trim($query, '&');

			if (empty($url_parts['query'])) {
				$url .= '?' . $query;
			}
			else {
				$url = str_replace($url_parts['query'], $query, $url);
			}
		}
		return $url;
	}

	/**
	 * Whether this transport is valid
	 *
	 * @codeCoverageIgnore
	 * @return boolean True if the transport is valid, false otherwise.
	 */
	public static function test() {
		return (function_exists('curl_init') && function_exists('curl_exec'));
	}
}