<?php
/**
 * Exception for HTTP requests
 *
 * @package Requests
 */

/**
 * Exception for HTTP requests
 *
 * @package Requests
 */
class Requests_Exception extends Exception {
	protected $type;
	protected $data;

	public function __construct($message, $type, $data = null) {
		parent::__construct($message, 0);

		$this->type = $type;
		$this->data = $data;
	}

	/**
	 * Like {@see getCode()}, but a string code.
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Gives any relevant data
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}
}