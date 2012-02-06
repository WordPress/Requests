<?php

class Requests_Hooks {
	public function __construct() {
		// pass
	}

	public function register($hook, $callback, $priority = 0) {
		if (!isset($this->hooks[$hook])) {
			$this->hooks[$hook] = array();
		}
		if (!isset($this->hooks[$hook][$priority])) {
			$this->hooks[$hook][$priority] = array();
		}

		$this->hooks[$hook][$priority][] = $callback;
	}

	public function dispatch($hook, $parameters = array()) {
		if (empty($this->hooks[$hook])) {
			return false;
		}

		foreach ($this->hooks[$hook] as $priority => $hooked) {
			foreach ($hooked as $callback) {
				call_user_func_array($callback, $parameters);
			}
		}

		return true;
	}
}