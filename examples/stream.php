<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * @package   Requests\Examples
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

require_once dirname(__DIR__) . '/src/Autoload.php';

WpOrg\Requests\Autoload::register();

$response = WpOrg\Requests\Requests::get(
	'https://httpbin.dev/stream/100',
	['Accept' => 'application/json'],
	['stream' => true] // Enables streaming mode.
);

echo 'Status: ', $response->status_code, PHP_EOL;
echo 'Content-Type: ', $response->headers['content-type'], PHP_EOL, PHP_EOL;

$buffer = '';
$lines  = 0;

while (!$response->eof()) {
	$buffer .= $response->read(8192);

	while (($pos = strpos($buffer, "\n")) !== false) {
		$line   = substr($buffer, 0, $pos);
		$buffer = substr($buffer, $pos + 1);

		if (trim($line) === '') {
			continue;
		}

		var_dump(json_decode($line, true));
		++$lines;
	}
}

echo PHP_EOL, 'Processed ', $lines, ' streamed lines.', PHP_EOL;

$response->close();
