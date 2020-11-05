<?php

class RequestsTestCaseBase extends PHPUnit_Framework_TestCase {
    protected function setUp()
    {
        if ( method_exists( $this, '_setUp' ) ) {
            $this->_setUp();
        }
    }
}
