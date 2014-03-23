<?php

ini_set('html_errors', false);
header('Content-Type: application/json; charset=utf-8');

class Response {
	public static function redirect ($path, $code = 302, $relative = false) {
		global $base_url;
		$url = $path;
		if (!$relative) {
			$url = $base_url . $path;
		}

		header('Location: ' . $url, true, $code);
	}

	public static function generate_post_data() {
		global $request_data;
		$data = $request_data;
		$data['data'] = file_get_contents('php://input');

		$data['form'] = '';
		if (strpos($data['data'], '&') !== false)
			$data['form'] = parse_params_rfc($data['data']);

		$data['json'] = json_decode($data['data']);

		$data['files'] = array_map(function ($data) {
			return file_get_contents($data['tmp_name']);
		}, $_FILES);

		return $data;
	}
}

function parse_params_rfc($input) {
	if (!isset($input) || !$input) return array();

	$pairs = explode('&', $input);

	$parsed = array();
	foreach ($pairs as $pair) {
		$split = explode('=', $pair, 2);
		$parameter = urldecode($split[0]);
		$value = isset($split[1]) ? urldecode($split[1]) : '';
		$parsed[$parameter] = $value;
	}
	return $parsed;
}

$base_url = 'http://' . $_SERVER['HTTP_HOST'];

$request_data = [
	'url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
	'headers' => apache_request_headers(),
	'origin' => $_SERVER['REMOTE_ADDR'],
	'args' => empty($_SERVER['QUERY_STRING']) ? new stdClass : parse_params_rfc( $_SERVER['QUERY_STRING'] ),
];

$routes = [];

// Request data!
$routes['/get'] = function () use ($request_data) {
	if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
		throw new Exception('Method not allowed', 405);
	}
	return $request_data;
};
$routes['/post'] = function () {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Method not allowed', 405);
	}

	return Response::generate_post_data();
};
$routes['/put'] = function () {
	if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
		throw new Exception('Method not allowed', 405);
	}

	return Response::generate_post_data();
};
$routes['/patch'] = function () {
	if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
		throw new Exception('Method not allowed', 405);
	}

	return Response::generate_post_data();
};
$routes['/delete'] = function () {
	if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
		throw new Exception('Method not allowed', 405);
	}

	return Response::generate_post_data();
};

// Cookies!
$routes['/cookies'] = function () {
	return [
		'cookies' => $_COOKIE,
	];
};
$routes['/cookies/set'] = function () {
	foreach ($_GET as $key => $value) {
		setcookie($key, $value, 0, '/');
	}

	Response::redirect('/cookies');
	exit;
};
$routes['/cookies/set/<key>/<value>'] = function ($args) {
	setcookie($args['key'], $args['value'], 0, '/');

	Response::redirect('/cookies');
	exit;
};
$routes['/cookies/delete'] = function () {
	foreach ($_GET as $key => $value) {
		setcookie($key, '', time() - 3600, '/');
	}

	Response::redirect('/cookies');
	exit;
};

$routes['/basic-auth/<user>/<password>'] = function ($args) {
	$supplied = [
		'user'     => empty($_SERVER['PHP_AUTH_USER']) ? false : $_SERVER['PHP_AUTH_USER'],
		'password' => empty($_SERVER['PHP_AUTH_PW'])   ? false : $_SERVER['PHP_AUTH_PW'],
	];

	if ($args['user'] !== $supplied['user'] || $args['password'] !== $supplied['password']) {
		http_response_code(401);
		header( 'WWW-Authenticate: Basic realm="Fake Realm"' );
		return;
	}

	return [
		'authenticated' => true,
		'user' => $args['user'],
	];
};

// Redirects!
$routes['/redirect/<number>'] = function ($args) use ($routes) {
	$num = (int) max((int) $args['number'], 1);
	if ($num === 1) {
		Response::redirect('/get');
		exit;
	}

	$num--;

	Response::redirect(sprintf('/redirect/%d', $num));
	exit;
};
$routes['/redirect-to'] = function () {
	$location = $_GET['url'];
	header('Location: ' . $location, true, 302);
	exit;
};
$routes['/relative-redirect/<number>'] = function ($args) {
	$num = (int) max((int) $args['number'], 1);
	if ($num === 1) {
		Response::redirect('/get', 302, true);
		exit;
	}

	$num--;

	Response::redirect(sprintf('/relative-redirect/%d', $num), 302, true);
	exit;
};

// Miscellaneous!
$routes['/delay/<delay>'] = function ($args) use ($routes) {
	$delay = min($args['delay'], 10);
	sleep($delay);

	return $routes['/get'];
};
$routes['/status/<code>'] = function ($args) use ($base_url) {
	$code = (int) $args['code'];

	switch ($code) {
		case 301:
		case 302:
		case 303:
		case 307:
			header('Location: ' . $base_url . '/get');
			break;

		case 401:
			header('WWW-Authenticate: Basic realm="Fake Realm"');
			break;

		case 407:
			header('Proxy-Authenticate: Basic realm="Fake Realm"');
			break;
	}


	http_response_code($code);
	exit;
};
$routes['/stream/<num>'] = function ($args) use ($request_data) {
	$response = $request_data;
	$num = min($args['num'], 100);
	$generate_stream = function () use ($num, $response) {
		foreach (range(0, $num - 1) as $n) {
			$response['id'] = $n;
			yield json_encode( $response, JSON_PRETTY_PRINT ) . "\n";
		}
	};

	header('Transfer-Encoding: chunked');
	foreach ( $generate_stream() as $response ) {
		printf("%x\r\n%s\r\n", strlen($response), $response);
		flush();
	}
	echo "0\r\n\r\n";
	exit;
};
$routes['/gzip'] = function () use ($request_data) {
	$response = $request_data;
	$response['gzipped'] = true;

	$response = json_encode($response, JSON_PRETTY_PRINT);
	$response = gzencode($response, 4, FORCE_GZIP);

	header('Content-Encoding: gzip');
	header('Content-Length: ' . strlen($response));

	echo $response;
	exit;
};

// Finally, the index!
$routes['/'] = function () use ($routes) {
	header('Content-Type: text/html; charset=utf-8');

	echo '<ul>';
	foreach ($routes as $url => $_) {
		echo '<li><code>' . htmlspecialchars( $url ) . '</code></li>';
	}
	echo '</ul>';
	exit;
};

$data = null;

try {
	foreach ($routes as $route => $callback) {
		$route = preg_replace('#<(\w+)>#i', '(?P<\1>\w+)', $route);
		$match = preg_match('#^' . $route . '$#i', $_SERVER['SCRIPT_NAME'], $matches);
		if (empty($match))
			continue;

		$data = $callback;
		break;
	}

	if (empty($data)) {
		throw new Exception('Requested URL not found', 404);
	}

	while (is_callable($data)) {
		$data = call_user_func($data, $matches);
	}
}
catch (Exception $e) {
	http_response_code($e->getCode());
	$data = [ 'message' => $e->getMessage() ];
}

echo json_encode($data, JSON_PRETTY_PRINT);