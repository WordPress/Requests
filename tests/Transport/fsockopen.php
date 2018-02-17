<?php

class RequestsTest_Transport_fsockopen extends RequestsTest_Transport_Base {
	protected $transport = 'Requests_Transport_fsockopen';

	public function testContentLengthHeader() {
		$hooks = new Requests_Hooks();
		$hooks->register('fsockopen.after_headers', array($this, 'checkContentLengthHeader'));

		$request = Requests::post(httpbin('/post'), array(), array(), $this->getOptions(array('hooks' => $hooks)));
	}

	public function checkContentLengthHeader($headers) {
		$this->assertContains('Content-Length: 0', $headers);
	}
}
