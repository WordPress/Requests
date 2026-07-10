<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Response\Stream;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Response\Stream;

/**
 * Response body stream backed by a cURL multi handle.
 *
 * @internal
 *
 * @package Requests
 */
final class Curl extends Stream {

	/**
	 * Maximum time (in seconds) to wait for activity per `curl_multi_select()` call.
	 *
	 * @var float
	 */
	const SELECT_TIMEOUT = 1.0;

	/**
	 * cURL multi handle driving the transfer.
	 *
	 * @var resource|\CurlMultiHandle|null Resource in PHP < 8.0, instance of CurlMultiHandle in PHP >= 8.0.
	 */
	private $multi;

	/**
	 * cURL easy handle for the request.
	 *
	 * @var resource|\CurlHandle|null Resource in PHP < 8.0, instance of CurlHandle in PHP >= 8.0.
	 */
	private $handle;

	/**
	 * Maximum time (in seconds) to wait for body data per read.
	 *
	 * @var int|float
	 */
	private $idle_timeout;

	/**
	 * Body bytes received from cURL, but not yet returned to the caller.
	 *
	 * @var string
	 */
	private $buffer = '';

	/**
	 * Whether the underlying transfer has finished.
	 *
	 * @var bool
	 */
	private $complete = false;

	/**
	 * Constructor.
	 *
	 * @param resource|\CurlMultiHandle   $multi        cURL multi handle with the easy handle already attached.
	 * @param resource|\CurlHandle        $handle       cURL easy handle for the request.
	 * @param string                      $prebuffered  Body bytes already received while reading the headers.
	 * @param int|float                   $idle_timeout Maximum time (in seconds) to wait for body data per read.
	 * @param int|false                   $max_bytes    Maximum number of body bytes to read, or false for no limit.
	 * @param \WpOrg\Requests\HookManager $hooks        Hook dispatcher.
	 */
	public function __construct($multi, $handle, $prebuffered, $idle_timeout, $max_bytes, $hooks) {
		parent::__construct($max_bytes, $hooks);

		$this->multi        = $multi;
		$this->handle       = $handle;
		$this->buffer       = (string) $prebuffered;
		$this->idle_timeout = $idle_timeout;

		curl_setopt($handle, CURLOPT_WRITEFUNCTION, [$this, 'receive_body']);
		curl_setopt($handle, CURLOPT_HEADERFUNCTION, [$this, 'receive_trailer']);
	}

	/**
	 * Discard trailer headers received after the body started.
	 *
	 * @param resource|\CurlHandle $handle cURL easy handle.
	 * @param string               $data   Header data received.
	 * @return int Length of the provided data.
	 */
	public function receive_trailer($handle, $data) {
		return strlen($data);
	}

	/**
	 * Collect body data as cURL receives it.
	 *
	 * @param resource|\CurlHandle $handle cURL easy handle.
	 * @param string               $data   Body data received.
	 * @return int Number of bytes handled. Returning fewer bytes than received
	 *             makes libcurl abort the transfer.
	 */
	public function receive_body($handle, $data) {
		$length = strlen($data);

		if ($this->max_bytes !== false) {
			$buffered = $this->bytes_read + strlen($this->buffer);
			if (($buffered + $length) > $this->max_bytes) {
				$length = $this->max_bytes - $buffered;
				$data   = substr($data, 0, $length);
			}
		}

		$this->buffer .= $data;

		return $length;
	}

	/**
	 * Advance the transfer and return received body bytes.
	 *
	 * @param int $length Maximum number of bytes to return.
	 * @return string
	 *
	 * @throws \WpOrg\Requests\Exception On a cURL error.
	 * @throws \WpOrg\Requests\Exception When no data arrives within the timeout.
	 */
	protected function fetch($length) {
		$deadline = microtime(true) + $this->idle_timeout;

		while ($this->buffer === '' && $this->complete === false) {
			$running = 0;
			do {
				$status = curl_multi_exec($this->multi, $running);
			} while ($status === CURLM_CALL_MULTI_PERFORM);

			while ($done = curl_multi_info_read($this->multi)) {
				if ($done['handle'] !== $this->handle) {
					continue;
				}

				$this->complete = true;

				$bytes_received = $this->bytes_read + strlen($this->buffer);
				if ($done['result'] === CURLE_WRITE_ERROR
					&& $this->max_bytes !== false
					&& $bytes_received >= $this->max_bytes
				) {
					continue;
				}

				if ($done['result'] !== CURLE_OK) {
					$error = sprintf(
						'cURL error %s: %s',
						$done['result'],
						curl_error($this->handle)
					);
					throw new Exception($error, 'curlerror', $this->handle);
				}
			}

			if ($this->buffer !== '' || $this->complete === true) {
				break;
			}

			if ($running === 0) {
				$this->complete = true;
				break;
			}

			$remaining = $deadline - microtime(true);
			if ($remaining <= 0) {
				throw new Exception('Stream timed out while waiting for body data', 'timeout', $this->handle);
			}

			if (curl_multi_select($this->multi, min(self::SELECT_TIMEOUT, $remaining)) === -1) {
				// Some platforms return -1 with nothing to wait on; back off
				// briefly to avoid a busy-wait.
				usleep(1000);
			}
		}

		// substr() can return false on PHP < 8.0, hence the casts.
		$out          = (string) substr($this->buffer, 0, $length);
		$this->buffer = (string) substr($this->buffer, strlen($out));

		return $out;
	}

	/**
	 * Release the cURL handles.
	 *
	 * @return void
	 */
	protected function free() {
		if ($this->handle !== null) {
			curl_setopt($this->handle, CURLOPT_WRITEFUNCTION, null);
			curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, null);

			if ($this->multi !== null) {
				curl_multi_remove_handle($this->multi, $this->handle);
			}

			if (is_resource($this->handle)) {
				// phpcs:ignore PHPCompatibility.FunctionUse.RemovedFunctions.curl_closeDeprecated,Generic.PHP.DeprecatedFunctions.Deprecated
				curl_close($this->handle);
			}

			$this->handle = null;
		}

		if ($this->multi !== null) {
			curl_multi_close($this->multi);
			$this->multi = null;
		}
	}
}
