<?php
/**
 * File handler for file uploads
 *
 * @package Requests
 * @subpackage Utilities
 */

namespace WpOrg\Requests\Utility;


use WpOrg\Requests\Exception\RequestsExceptionFile;
/**
 * A file object describing an upload.
 *
 * Used in the $data parameter to POST requests.
 *
 * @package Requests
 * @subpackage Utilities
 */
class RequestsFile {
	/**
	 * The path to the file.
	 *
	 * @var string|null
	 */
	public $path = null;

	/**
	 * The file mimetype.
	 *
	 * @var string|null
	 */
	public $type = null;

	/**
	 * Override the file name when uploading.
	 *
	 * @var string|null
	 */
	public $name = null;

	/**
	 * Create a file wrapper.
	 *
	 * @param string $filepath The path to the file.
	 * @param string $mimetype The mimetype override. Will try to guess if not given.
	 * @param string $filename The upload file name.
	 *
	 * @return RequestsFile
	 *@throws RequestsExceptionFile If file is not readable or does not exist.
	 *
	 */
	public function __construct($path, $type = null, $name = null) {
		if (!file_exists($path) || !is_readable($path)) {
			throw new RequestsExceptionFile('File is not readable', null, $path);
		}

		$this->path = $path;
		$this->type = $type ? $type : mime_content_type($path);
		$this->name = $name ? $name : basename($path);
	}

	/**
	 * Retrieve the contents into a string.
	 *
	 * Caution: large files will fill up the RAM.
	 *
	 * @return string The contents.
	 */
	public function get_contents() {
		return file_get_contents($this->path);
	}
}
