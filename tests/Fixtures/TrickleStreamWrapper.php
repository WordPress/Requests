<?php

namespace WpOrg\Requests\Tests\Fixtures;

final class TrickleStreamWrapper {

	const NAME = 'trickle';

	public $context;
	private $blocks = [];

	public static $script = [];

	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Signature defined by the PHP stream wrapper API.
	public function stream_open($path, $mode, $options, &$opened_path) {
		$this->blocks = self::$script;
		return true;
	}

	public function stream_read($count) {
		if ($this->blocks === []) {
			return '';
		}

		$block = array_shift($this->blocks);
		if (strlen($block) > $count) {
			// Return what fits and re-queue the remainder for the next read.
			array_unshift($this->blocks, substr($block, $count));
			$block = substr($block, 0, $count);
		}

		return $block;
	}

	public function stream_eof() {
		return $this->blocks === [];
	}

	public function stream_stat() {
		return [];
	}

	public function stream_close() {
		$this->blocks = [];
	}
}
