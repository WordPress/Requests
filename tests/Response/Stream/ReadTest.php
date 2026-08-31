<?php

namespace WpOrg\Requests\Tests\Response\Stream;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Response\Stream\Socket;
use WpOrg\Requests\Tests\Fixtures\TrickleStreamWrapper;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Response\Stream::read
 * @covers \WpOrg\Requests\Response\Stream::eof
 * @covers \WpOrg\Requests\Response\Stream\Socket::__construct
 * @covers \WpOrg\Requests\Response\Stream\Socket::fetch
 */
final class ReadTest extends TestCase {

	public static function set_up_before_class() {
		parent::set_up_before_class();
		stream_wrapper_register(TrickleStreamWrapper::NAME, TrickleStreamWrapper::class);
	}

	public static function tear_down_after_class() {
		stream_wrapper_unregister(TrickleStreamWrapper::NAME);
		parent::tear_down_after_class();
	}

	public function testReadReturnsFullBodyAcrossMultipleReads() {
		$stream = $this->streamFromString('Hello, streaming world!');

		$body = '';
		while (!$stream->eof()) {
			$chunk = $stream->read(8);
			$this->assertLessThanOrEqual(8, strlen($chunk), 'read() returned more bytes than requested');
			$body .= $chunk;
		}

		$this->assertSame('Hello, streaming world!', $body);
	}

	public function testEofIsReachedOnceDrained() {
		$stream = $this->streamFromString('abc');

		$this->assertFalse($stream->eof(), 'Stream should not be at EOF before reading');

		$stream->read(1024);
		$this->assertSame('', $stream->read(1024), 'Reading past the end should return an empty string');
		$this->assertTrue($stream->eof(), 'Stream should be at EOF once drained');
	}

	public function testReadDecodesChunkedBody() {
		$stream = $this->streamFromString("4\r\nWiki\r\n5\r\npedia\r\n0\r\n\r\n", true);

		$this->assertSame('Wikipedia', $this->drain($stream));
	}

	public function testReadDecodesChunkedBodyDeliveredInFragments() {
		// First read yields framing only (no body bytes), later reads split a
		// chunk's data section.
		$stream = $this->streamFromBlocks(["4\r\n", 'Wi', "ki\r\n", "5\r\npedia\r\n", "0\r\n\r\n"], true);
		$this->assertSame('Wikipedia', $this->drain($stream), 'Body should survive framing-only reads');

		// The chunk-size line itself is torn in the middle of its CRLF.
		$stream = $this->streamFromBlocks(["9\r", "\nstreaming", "\r\n0\r\n\r\n"], true);
		$this->assertSame('streaming', $this->drain($stream), 'Body should survive a torn chunk-size line');
	}

	public function testReadDoesNotTreatEmptyReadAsEndOfBody() {
		$stream = $this->streamFromBlocks(['Hello', '', ' world']);

		$this->assertSame('Hello world', $this->drain($stream));
	}

	public function testReadRespectsMaxBytes() {
		$stream = $this->streamFromString(str_repeat('x', 1000), false, 100);

		$this->assertSame(100, strlen($this->drain($stream)));
		$this->assertTrue($stream->eof(), 'Stream should be at EOF once max_bytes is reached');
	}

	public function testReadFiresProgressHook() {
		$captured = [];
		$hooks    = new Hooks();
		$hooks->register(
			'request.progress',
			function ($data, $bytes_so_far, $max_bytes) use (&$captured) {
				$captured[] = [$data, $bytes_so_far, $max_bytes];
			}
		);

		$stream = $this->streamFromString('abcdefghij', false, false, $hooks);
		$stream->read(4);
		$stream->read(4);

		$this->assertSame(
			[
				['abcd', 0, false],
				['efgh', 4, false],
			],
			$captured
		);
	}

	/**
	 * @dataProvider dataReadInvalidLength
	 */
	public function testReadInvalidLength($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('::read(): Argument #1 ($length) must be of type positive integer');

		$this->streamFromString('abc')->read($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public static function dataReadInvalidLength() {
		return array_merge(
			TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_INT),
			TypeProviderHelper::getSelection(['integer 0', 'negative integer'])
		);
	}

	/**
	 * Build a socket-backed stream serving a fixed string from memory.
	 *
	 * @param string                      $data      Raw body bytes.
	 * @param bool                        $chunked   Whether to treat the body as chunked.
	 * @param int|false                   $max_bytes Maximum number of bytes to read.
	 * @param \WpOrg\Requests\Hooks|null  $hooks     Optional. Hook dispatcher.
	 *
	 * @return \WpOrg\Requests\Response\Stream\Socket
	 */
	private function streamFromString($data, $chunked = false, $max_bytes = false, $hooks = null) {
		$handle = fopen('php://temp', 'r+b');
		fwrite($handle, $data);
		rewind($handle);

		if ($hooks === null) {
			$hooks = new Hooks();
		}

		return new Socket($handle, $chunked, $max_bytes, $hooks);
	}

	/**
	 * Build a socket-backed stream serving predefined blocks one read at a time.
	 *
	 * @param array<string> $blocks  Blocks to serve, one per read.
	 * @param bool          $chunked Whether to treat the body as chunked.
	 *
	 * @return \WpOrg\Requests\Response\Stream\Socket
	 */
	private function streamFromBlocks($blocks, $chunked = false) {
		TrickleStreamWrapper::$script = $blocks;
		$handle                       = fopen(TrickleStreamWrapper::NAME . '://body', 'rb');

		return new Socket($handle, $chunked, false, new Hooks());
	}

	/**
	 * Read a stream to the end and return the collected body.
	 *
	 * @param \WpOrg\Requests\Response\Stream $stream Stream to drain.
	 *
	 * @return string
	 */
	private function drain($stream) {
		$body = '';
		while (!$stream->eof()) {
			$body .= $stream->read(4096);
		}

		return $body;
	}
}
