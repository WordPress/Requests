<?php
namespace Rmccue\Requests\Exception;

Use Rmccue\Requests as Requests;
Use Rmccue\Requests\Exception as Exception;

/**
 * Exception based on HTTP response
 *
 * @package Rmccue\Requests
 */

/**
 * Exception based on HTTP response
 *
 * @package Rmccue\Requests
 */
class HTTP extends Exception {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 0;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unknown';

	/**
	 * Create a new exception
	 *
	 * There is no mechanism to pass in the status code, as this is set by the
	 * subclass used. Reason phrases can vary, however.
	 *
	 * @param string|null $reason Reason phrase
	 * @param mixed $data Associated data
	 */
	public function __construct($reason = null, $data = null) {
		if ($reason !== null) {
			$this->reason = $reason;
		}

		$message = sprintf('%d %s', $this->code, $this->reason);
		parent::__construct($message, 'httpresponse', $data, $this->code);
	}

	/**
	 * Get the status message
	 */
	public function getReason() {
		return $this->reason;
	}

	/**
	 * Get the correct exception class for a given error code
	 *
	 * @param int|bool $code HTTP status code, or false if unavailable
	 * @return string Rmccue\Requests\Exception class name to use
	 */
	public static function get_class($code) {
		if (!$code) {
			return '\\Rmccue\\Requests\\Exception\\HTTP\\Unknown';
		}

		$class = sprintf('Rmccue\\Requests\\Exception\\HTTP\\Response%d', $code);
		if (class_exists($class)) {
			return $class;
		}

		return '\\Rmccue\\Requests\\Exception\\HTTP\\Unknown';
	}
}