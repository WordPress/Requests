<?php

namespace WpOrg\Requests\Tests\Utility\HostBindings;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;
use WpOrg\Requests\Utility\HostBindings;

/**
 * @covers \WpOrg\Requests\Utility\HostBindings::has_host
 */
final class HasHostTest extends TestCase {

    /**
     * Test checking for existing hosts.
     *
     * @return void
     */
    public function testHasExistingHost() {
        $bindings = [
            'example.com' => ['93.184.216.34'],
            'localhost' => ['127.0.0.1'],
        ];

        $host_bindings = new HostBindings($bindings);
        
        $this->assertTrue($host_bindings->has_host('example.com'));
        $this->assertTrue($host_bindings->has_host('localhost'));
        $this->assertFalse($host_bindings->has_host('nonexistent.com'));
    }

    /**
     * Test that invalid host parameter types throw an exception.
     *
     * @dataProvider dataInvalidTypes
     *
     * @param mixed $input Invalid input.
     *
     * @return void
     */
    public function testInvalidHostType($input) {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('$host');

        $host_bindings = new HostBindings(['example.com' => ['127.0.0.1']]);
        $host_bindings->has_host($input);
    }

    /**
     * Data Provider.
     *
     * @return array
     */
    public static function dataInvalidTypes() {
        return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
    }
} 