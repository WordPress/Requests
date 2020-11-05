<?php

class RequestsTestCase extends RequestsTestCaseBase {
    /**
     * PHPUnit 6+ compatibility shim.
     *
     * @param mixed      $exception
     * @param string     $message
     * @param int|string $code
     */
    public function setExpectedException($exception, $message = null, $code = null) {
        if (method_exists('PHPUnit_Framework_TestCase', 'setExpectedException')) {
            parent::setExpectedException($exception, $message, $code);
        } else {
            $this->expectException($exception);
            if ($message !== null) {
                $this->expectExceptionMessage($message);
            }
            if ($code !== null) {
                $this->expectExceptionCode($code);
            }
        }
    }
}
