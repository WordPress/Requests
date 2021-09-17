<?php

namespace WpOrg\Requests\Tests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Port;
use WpOrg\Requests\Tests\TestCase;

/**
 * @covers \WpOrg\Requests\Port::get
 */
final class PortTest extends TestCase {

	/**
	 * Test retrieving a port based on a passed input value.
	 *
	 * @dataProvider dataGetPort
	 *
	 * @param mixed $input    Input to pass to the function.
	 * @param int   $expected Expected function return value.
	 *
	 * @return void
	 */
	public function testGetPort($input, $expected) {
		$this->assertSame($expected, Port::get($input));
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataGetPort() {
		return array(
			'lowercase type' => array(
				'input'    => 'https',
				'expected' => Port::HTTPS,
			),
			'mixed type' => array(
				'input'    => 'Dict',
				'expected' => Port::DICT,
			),
			'uppercase type' => array(
				'input'    => 'ACAP',
				'expected' => Port::ACAP,
			),
		);
	}

	/**
	 * Test that when a $type parameter of an incorrect type gets passed, an exception gets thrown.
	 *
	 * @dataProvider dataGetPortThrowsExceptionOnInvalidInputType
	 *
	 * @param mixed $input Input to pass to the function.
	 *
	 * @return void
	 */
	public function testGetPortThrowsExceptionOnInvalidInputType($input) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($type) must be of type string');

		Port::get($input);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataGetPortThrowsExceptionOnInvalidInputType() {
		return array(
			'null'                => array(null),
			'integer port number' => array(443),
		);
	}

	/**
	 * Test that when an unsupported port type is requested, an exception gets thrown.
	 *
	 * @dataProvider dataGetPortThrowsExceptionOnUnsupportedPortType
	 *
	 * @param string $input Input to pass to the function.
	 *
	 * @return void
	 */
	public function testGetPortThrowsExceptionOnUnsupportedPortType($input) {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid port type');

		Port::get($input);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataGetPortThrowsExceptionOnUnsupportedPortType() {
		return array(
			'type not supported' => array('FTP'),
			'empty string'       => array(''),
		);
	}
}
