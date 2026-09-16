<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\Response;

use WpOrg\Requests\Exception\InvalidArgument;

/**
 * Readable response body stream.
 *
 * @package Requests
 */
abstract class Stream {

	/**
	 * Default number of bytes to request per read.
	 *
	 * @var int
	 */
	const DEFAULT_CHUNK_SIZE = 8192;

	/**
	 * Hook dispatcher, used to fire `request.progress` as body data is read.
	 *
	 * @var \WpOrg\Requests\HookManager
	 */
	protected $hooks;

	/**
	 * Maximum number of body bytes to read, or false for no limit.
	 *
	 * @var int|false
	 */
	protected $max_bytes = false;

	/**
	 * Number of body bytes already returned to the caller.
	 *
	 * @var int
	 */
	protected $bytes_read = 0;

	/**
	 * Whether the end of the body has been reached.
	 *
	 * @var bool
	 */
	protected $reached_eof = false;

	/**
	 * Constructor.
	 *
	 * @param int|false                   $max_bytes Maximum number of body bytes to read, or false for no limit.
	 * @param \WpOrg\Requests\HookManager $hooks     Hook dispatcher.
	 */
	protected function __construct($max_bytes, $hooks) {
		$this->max_bytes = $max_bytes;
		$this->hooks     = $hooks;
	}

	/**
	 * Read up to a number of bytes from the body.
	 *
	 * @param int $length Optional. Maximum number of bytes to read.
	 * @return string Body bytes, already de-chunked. An empty string indicates
	 *                the end of the body.
	 *
	 * @throws \WpOrg\Requests\Exception\InvalidArgument When the passed $length argument is not a positive integer.
	 */
	public function read($length = self::DEFAULT_CHUNK_SIZE) {
		if (is_int($length) === false || $length < 1) {
			throw InvalidArgument::create(1, '$length', 'positive integer', gettype($length));
		}

		if ($this->reached_eof === true) {
			return '';
		}

		if ($this->max_bytes !== false) {
			$remaining = $this->max_bytes - $this->bytes_read;
			if ($remaining <= 0) {
				$this->reached_eof = true;
				return '';
			}

			if ($length > $remaining) {
				$length = $remaining;
			}
		}

		$data = $this->fetch($length);
		if ($data === '') {
			// A blocking read returned nothing, so the body is exhausted.
			$this->reached_eof = true;
			return '';
		}

		$this->hooks->dispatch('request.progress', [$data, $this->bytes_read, $this->max_bytes]);

		$this->bytes_read += strlen($data);

		if ($this->max_bytes !== false && $this->bytes_read >= $this->max_bytes) {
			$this->reached_eof = true;
		}

		return $data;
	}

	/**
	 * Whether the end of the body has been reached.
	 *
	 * @return bool
	 */
	public function eof() {
		return $this->reached_eof;
	}

	/**
	 * Release the underlying resources.
	 *
	 * @return void
	 */
	public function close() {
		$this->free();
		$this->reached_eof = true;
	}

	/**
	 * Destructor: ensure the underlying resources are released.
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * Pull up to a number of bytes from the backend.
	 *
	 * @param int $length Maximum number of bytes to read.
	 * @return string
	 */
	abstract protected function fetch($length);

	/**
	 * Release the backend's resources (sockets, cURL handles, ...).
	 *
	 * @return void
	 */
	abstract protected function free();
}
