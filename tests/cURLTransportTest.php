<?php

class cURLTransportTest extends TransportTest {
	public function setUp() {
		Requests::$transport = 'Requests_Transport_cURL';
	}
}