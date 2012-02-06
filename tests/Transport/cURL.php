<?php

class RequestsTest_Transport_cURL extends RequestsTest_Transport_Base {
	public function setUp() {
		Requests::$transport = 'Requests_Transport_cURL';
	}
}