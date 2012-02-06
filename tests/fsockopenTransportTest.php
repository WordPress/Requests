<?php

class fsockopenTransportTest extends TransportTest {
	public function setUp() {
		Requests::$transport = 'Requests_Transport_fsockopen';
	}
}