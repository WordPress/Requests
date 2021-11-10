<?php
namespace WpOrg\Requests\Tests\Utility;

use WpOrg\Requests\Requests;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Exception\RequestsExceptionFile;
use WpOrg\Requests\Utility\RequestsFile;

class FileTest extends TestCase {

	/**
	 * @throws RequestsExceptionFile
	 *
	 * @covers \WpOrg\Requests\Exception\RequestsExceptionFile
	 */
	public function testInvalidFile() {
		$this->expectException( RequestsExceptionFile::class );
		new RequestsFile( sys_get_temp_dir() . 'null.exe');
	}

	/**
	 * @throws RequestsExceptionFile
	 *
	 * @covers \WpOrg\Requests\Utility\RequestsFile
	 */
	public function testBasic() {
		file_put_contents( $tmpfile = tempnam( sys_get_temp_dir(), 'requests' ), '');
		$file = new RequestsFile( $tmpfile, 'text/plain', 'readme.txt' );

		$this->assertEquals( $file->path, $tmpfile);
		$this->assertEquals( $file->type, 'text/plain' );
		$this->assertEquals( $file->name, 'readme.txt' );

		file_put_contents( $tmpfile = tempnam(sys_get_temp_dir(), 'requests' ), 'hello');
		$file = new RequestsFile($tmpfile);
		$this->assertEquals($file->name, basename( $tmpfile ) );

		$this->assertEquals( $file->get_contents(), 'hello' );
	}

	/**
	 * @throws RequestsExceptionFile
	 *
	 * @covers \WpOrg\Requests\Utility\RequestsFile
	 */
	public function testMime() {
		file_put_contents( $tmpfile = tempnam( sys_get_temp_dir(), 'requests' ), 'hello');
		$file = new RequestsFile( $tmpfile );
		$this->assertEquals( $file->type, 'text/plain' );

		file_put_contents( $tmpfile = tempnam(sys_get_temp_dir(), 'requests' ), "\xff\xd8\xff");
		$file = new RequestsFile($tmpfile);
		$this->assertEquals( $file->type, 'image/jpeg' );

		file_put_contents( $tmpfile = tempnam(sys_get_temp_dir(), 'requests' ), "\x78\x01");
		$file = new RequestsFile($tmpfile);
		$this->assertEquals( $file->type, 'application/octet-stream' );
	}

	/**
	 * @covers \WpOrg\Requests\Requests::add_files_to_body
	 */
	public function test_add_file_to_empty_body(){
		$body = '';
		file_put_contents( $tmpfile = tempnam( sys_get_temp_dir(), 'requests' ), 'hello');
		$file = new RequestsFile( $tmpfile );
		$body = Requests::add_files_to_body( $body, $tmpfile, 'filename' );
		$this->assertEquals( $body, array( 'filename' => $file ) );
	}

	/**
	 * @covers \WpOrg\Requests\Requests::add_files_to_body
	 */
	public function test_add_file_to_body_with_string(){
		$body = 'dd';
		file_put_contents( $tmpfile = tempnam( sys_get_temp_dir(), 'requests' ), 'hello');
		$file = new RequestsFile( $tmpfile );
		$body = Requests::add_files_to_body( $body, $tmpfile, 'filename' );
		$this->assertEquals( $body, array( 0 => 'dd','filename' => $file ) );
	}

	/**
	 * @covers \WpOrg\Requests\Requests::add_files_to_body
	 */
	public function test_add_file_to_body_with_array(){
		$body = array( 'dd' => 'fff' ) ;
		file_put_contents( $tmpfile = tempnam( sys_get_temp_dir(), 'requests' ), 'hello' );
		$file = new RequestsFile( $tmpfile );
		$body = Requests::add_files_to_body( $body, $tmpfile, 'filename' );
		$this->assertEquals( $body, array( 'dd' => 'fff','filename' => $file ) );
	}
	/**
	 * @covers \WpOrg\Requests\Requests::add_files_to_body
	 */
	public function test_add_files_to_body_with_array(){
		$body = array( 'dd' => 'fff' ) ;
		file_put_contents( $tmpfile1 = tempnam( sys_get_temp_dir(), 'requests' ), 'hello' );
		$file1 = new RequestsFile( $tmpfile1 );
		file_put_contents( $tmpfile2 = tempnam( sys_get_temp_dir(), 'requests' ), 'hello2' );
		$file2 = new RequestsFile( $tmpfile2 );
		$files = array( $tmpfile1, $tmpfile2 );
		$body = Requests::add_files_to_body( $body, $files );
		$this->assertEquals( $body, array( 'dd' => 'fff',0 => $file1, 1 => $file2 ) );
	}
	/**
	 * @covers \WpOrg\Requests\Requests::add_files_to_body
	 */
	public function test_add_files_to_body_with_keyed_array(){
		$body = array( 'dd' => 'fff' ) ;
		file_put_contents( $tmpfile1 = tempnam( sys_get_temp_dir(), 'requests' ), 'hello' );
		$file1 = new RequestsFile( $tmpfile1 );
		file_put_contents( $tmpfile2 = tempnam( sys_get_temp_dir(), 'requests' ), 'hello2' );
		$file2 = new RequestsFile( $tmpfile2 );
		$files = array( 'tmpfile1' => $tmpfile1,'tmpfile2' => $tmpfile2 );
		$body = Requests::add_files_to_body( $body, $files );
		$this->assertEquals( $body, array( 'dd' => 'fff','tmpfile1' => $file1, 'tmpfile2' => $file2 ) );
	}
}
