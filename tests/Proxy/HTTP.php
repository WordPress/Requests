<?php

// Local fake proxy details
define( 'PROXYTEST_PROXY', 'localhost:3128' );
define( 'PROXYTEST_USER',  'joe' );
define( 'PROXYTEST_PASS',  'derp' );
// define( 'PROXYTEST_URL',   'http://httpbin.org/headers' );
define( 'PROXYTEST_URL',   'http://127.0.0.1/headers.php' );


class Requests_Proxy_Add_HTTP_XHeader implements Requests_Proxy {

	public function __construct( $proxy, $user = null, $pass = null ) {
		$this->proxy = $proxy;
		$this->user = $user;
		$this->pass = $pass;
	}

	public function register(Requests_Hooks &$hooks) {
		$hooks->register('requests.before_request', array(&$this, 'before_request'));
	}

	public function before_request(&$url, &$headers, &$data, &$type, &$options) {
		$headers['X-Requests-Proxy'] = 'HTTP';
		if( isset( $this->user ) && isset( $this->pass ) ) {
			$options = array(
				'proxy' => array( $this->proxy, $this->user, $this->pass )
			);
		} else {
			$options = array(
				'proxy' => $this->proxy
			);
		}
	}
}

class RequestsTest_Proxy_HTTP extends PHPUnit_Framework_TestCase {

	public static function transportProvider() {
		$transports = array(
			array('Requests_Transport_fsockopen'),
			array('Requests_Transport_cURL'),
		);
		return $transports;
	}
	
	/**
	 * @dataProvider transportProvider
	 */
	public function testProxyNoAuth( $transport ) {
		if (!call_user_func(array($transport, 'test'))) {
			$this->markTestSkipped($transport . ' is not available');
			return;
		}

		$options = array(
			'proxy' => new Requests_Proxy_Add_HTTP_XHeader( PROXYTEST_PROXY ),
			'transport' => $transport,
		);

		$request = Requests::get( PROXYTEST_URL, array(), $options );
		$this->assertEquals( 200, $request->status_code );

		$result = json_decode( $request->body );
		$this->assertEquals( 'HTTP', $result->{"X-Requests-Proxy"} );
		$this->assertEquals( 'HTTP', $result->{"X-Requests-Proxied"} );
	}

	/**
	 * @dataProvider transportProvider
	 */
	public function testProxyWithAuth( $transport ) {
		if (!call_user_func(array($transport, 'test'))) {
			$this->markTestSkipped($transport . ' is not available');
			return;
		}

		$options = array(
			'proxy' => new Requests_Proxy_Add_HTTP_XHeader( PROXYTEST_PROXY, PROXYTEST_USER, PROXYTEST_PASS ),
			'transport' => $transport,
		);
		$request = Requests::get( PROXYTEST_URL, array(), $options );
		$this->assertEquals( 200, $request->status_code );

		$result = json_decode( $request->body );
		$this->assertEquals( 'HTTP', $result->{"X-Requests-Proxy"} );
		$this->assertEquals( 'HTTP', $result->{"X-Requests-Proxied"} );
	}
	
	/**
	 * @expectedException Requests_Exception
	 */
	public function testMissingPassword() {
		$test = new Requests_Proxy_HTTP( array( PROXYTEST_PROXY, PROXYTEST_USER ) );
	}
	
}