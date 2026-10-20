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
 * Response body stream backed by a socket.
 *
 * @internal
 *
 * @package Requests
 */
final class Socket extends Stream {

	/**
	 * Socket to read from.
	 *
	 * @var resource|null
	 */
	private $socket;

	/**
	 * Constructor.
	 *
	 * @param resource                    $socket    Open socket, positioned at the start of the body.
	 * @param bool                        $chunked   Whether the response uses chunked transfer-encoding.
	 * @param int|false                   $max_bytes Maximum number of body bytes to read, or false for no limit.
	 * @param \WpOrg\Requests\HookManager $hooks     Hook dispatcher.
	 */
	public function __construct($socket, $chunked, $max_bytes, $hooks) {
		parent::__construct($max_bytes, $hooks);

		$this->socket = $socket;

		if ($chunked === true) {
			// Strip the chunked transfer-encoding framing while reading.
			stream_filter_append($socket, 'dechunk', STREAM_FILTER_READ);
		}
	}

	/**
	 * Read from the socket.
	 *
	 * @param int $length Maximum number of bytes to read.
	 * @return string
	 *
	 * @throws \WpOrg\Requests\Exception On socket timeout (`timeout`).
	 */
	protected function fetch($length) {
		if (is_resource($this->socket) === false) {
			return '';
		}

		while (true) {
			// If PHP's stream buffer already holds data (body bytes pulled in
			// while the headers were read), only ask for that much. Asking for
			// more makes fread() block for a top-up instead of returning the
			// buffered bytes right away.
			$info = stream_get_meta_data($this->socket);
			if (!empty($info['unread_bytes']) && $info['unread_bytes'] < $length) {
				$length = $info['unread_bytes'];
			}

			$block = fread($this->socket, $length);

			if ($block !== false && $block !== '') {
				// On PHP < 8.3, a read which hits the socket timeout can still
				// return the data received up to that point, with the timed_out
				// flag set. Data wins; the next read gets a fresh timeout.
				return $block;
			}

			$info = stream_get_meta_data($this->socket);
			if (!empty($info['timed_out'])) {
				throw new Exception('fsocket timed out', 'timeout');
			}

			if ($block === false) {
				// Read error; treat as the end of the body.
				return '';
			}

			if (feof($this->socket)) {
				return '';
			}

			// Nothing came back but the connection is still open. This happens
			// when a read filter (dechunk) swallows framing bytes without
			// producing body bytes. Try again; fread() blocks until more data
			// arrives, so this won't spin.
		}
	}

	/**
	 * Close the socket.
	 *
	 * @return void
	 */
	protected function free() {
		if (is_resource($this->socket)) {
			fclose($this->socket);
		}

		$this->socket = null;
	}
}
