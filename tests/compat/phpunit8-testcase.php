<?php

class RequestsTestCaseBase extends PHPUnit\Framework\TestCase {
    protected function setUp(): void
    {
        if ( method_exists( $this, '_setUp' ) ) {
            $this->_setUp();
        }
    }
}
