<?php

class RequestsTest_File extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException Requests_Exception_File
	 */
	public function testInvalidFile() {
		new Requests_File(sys_get_temp_dir() . 'null.exe');
	}

	public function testBasic() {
		file_put_contents($tmpfile = tempnam(sys_get_temp_dir(), 'requests'), '');
		$file = new Requests_File($tmpfile, 'text/plain', 'readme.txt');

		$this->assertEquals($file->path, $tmpfile);
		$this->assertEquals($file->type, 'text/plain');
		$this->assertEquals($file->name, 'readme.txt');

		file_put_contents($tmpfile = tempnam(sys_get_temp_dir(), 'requests'), 'hello');
		$file = new Requests_File($tmpfile);
		$this->assertEquals($file->name, basename($tmpfile));

		$this->assertEquals($file->get_contents(), 'hello');
	}

	public function testMime() {
		file_put_contents($tmpfile = tempnam(sys_get_temp_dir(), 'requests'), 'hello');
		$file = new Requests_File($tmpfile);
		$this->assertEquals($file->type, 'text/plain');

		file_put_contents($tmpfile = tempnam(sys_get_temp_dir(), 'requests'), "\xff\xd8\xff");
		$file = new Requests_File($tmpfile);
		$this->assertEquals($file->type, 'image/jpeg');

		file_put_contents($tmpfile = tempnam(sys_get_temp_dir(), 'requests'), "\x78\x01");
		$file = new Requests_File($tmpfile);
		$this->assertEquals($file->type, 'application/octet-stream');
	}
}
