<?php

namespace WpOrg\Requests\Tests\Utility\HostBindings;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\HostBindings;

/**
 * @covers \WpOrg\Requests\Utility\HostBindings::__construct
 */
final class ConstructorTest extends TestCase {

    /**
     * Test that valid host bindings are accepted by the constructor.
     *
     * @return void
     */
    public function testValidHostBindings() {
        $bindings = [
            'example.com' => ['93.184.216.34'],
            'localhost' => ['127.0.0.1', '::1'],
        ];

        $host_bindings = new HostBindings($bindings);
        $this->assertInstanceOf(HostBindings::class, $host_bindings);
    }

    /**
     * Test that invalid types for host_bindings parameter throw an exception.
     *
     * @dataProvider dataInvalidTypes
     *
     * @param mixed $input Invalid input.
     *
     * @return void
     */
    public function testInvalidTypes($input) {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('Argument #1 ($host_bindings) must be of type array');

        new HostBindings($input);
    }

    /**
     * Data Provider.
     *
     * @return array
     */
    public static function dataInvalidTypes() {
        return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
    }

    /**
     * Test that invalid host keys throw an exception.
     *
     * @dataProvider dataInvalidHostKeys
     *
     * @param mixed $key Invalid host key.
     *
     * @return void
     */
    public function testInvalidHostKeys($key) {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('$host_bindings (host)');

        new HostBindings([$key => ['127.0.0.1']]);
    }

    /**
     * Data Provider.
     *
     * @return array
     */
    public static function dataInvalidHostKeys() {
        return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
    }

    /**
     * Test that invalid IP address arrays throw an exception.
     *
     * @dataProvider dataInvalidIpArrays
     *
     * @param mixed $ips Invalid IP array.
     *
     * @return void
     */
    public function testInvalidIpArrays($ips) {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('$host_bindings (ip address array)');

        new HostBindings(['example.com' => $ips]);
    }

    /**
     * Data Provider.
     *
     * @return array
     */
    public static function dataInvalidIpArrays() {
        return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_ARRAY);
    }

    /**
     * Test that invalid IP addresses throw an exception.
     *
     * @dataProvider dataInvalidIpAddresses
     *
     * @param mixed $ip Invalid IP address.
     *
     * @return void
     */
    public function testInvalidIpAddresses($ip) {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('$host_bindings (ip address)');

        new HostBindings(['example.com' => [$ip]]);
    }

    /**
     * Data Provider.
     *
     * @return array
     */
    public static function dataInvalidIpAddresses() {
        return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
    }
} 