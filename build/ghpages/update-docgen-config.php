#!/usr/bin/env php
<?php
/**
 * Update the phpDocumentor configuration file.
 *
 * {@internal This functionality has a minimum PHP requirement of PHP 7.2.}
 *
 * @internal
 *
 * @package Requests
 * @subpackage GHPages
 *
 * @phpcs:disable PHPCompatibility.FunctionUse.NewFunctionParameters.dirname_levelsFound
 */

namespace WpOrg\Requests\GHPages;

use WpOrg\Requests\Autoload;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response;

$requests_phpdoc_version_updater = function () {
	// Include Requests.
	$project_root = dirname(__DIR__, 2);
	require_once $project_root . '/src/Autoload.php';
	Autoload::register();

	// Retrieve the information about the latest release from the GH API.
	$response = Requests::get(
		'https://api.github.com/repos/WordPress/Requests/releases/latest',
		array(
			'Accept' => 'application/vnd.github.v3+json',
		)
	);

	if (!($response instanceof Response) || $response->success !== true || $response->status_code !== 200) {
		echo 'ERROR: GH API request to retrieve the version number of the last release failed.', PHP_EOL;
		exit(1);
	}

	$decoded = json_decode($response->body, true);
	if (!isset($decoded['tag_name'])) {
		echo 'ERROR: GH API request to retrieve the version number of the last release failed to retrieve a version number.', PHP_EOL;
		exit(1);
	}

	$tagname = ltrim($decoded['tag_name'], 'v');

	if (file_exists($project_root . '/phpdoc.xml')) {
		echo 'WARNING: Detected pre-existing "phpdoc.xml" file.', PHP_EOL;
		echo 'Please make sure that this overload file is in sync with the "phpdoc.dist.xml" file.', PHP_EOL;
		echo 'This is your own responsibility!' . PHP_EOL, PHP_EOL;

		$config = file_get_contents($project_root . '/phpdoc.xml');
		if (!$config) {
			echo 'ERROR: Failed to read phpDocumentor configuration template file.', PHP_EOL;
			exit(1);
		}

		// Replace the previous version nr in the API doc title with the latest version number.
		$config = preg_replace(
			'`<title>Requests ([\#0-9\.]+) API</title>`',
			"<title>Requests {$tagname} API</title>",
			$config
		);
	} else {
		$config = file_get_contents($project_root . '/phpdoc.dist.xml');
		if (!$config) {
			echo 'ERROR: Failed to read phpDocumentor configuration template file.', PHP_EOL;
			exit(1);
		}

		// Replace the "#.#.#" placeholder in the API doc title with the latest version number.
		$config = str_replace(
			'<title>Requests #.#.# API</title>',
			"<title>Requests {$tagname} API</title>",
			$config
		);
	}

	if (file_put_contents($project_root . '/phpdoc.xml', $config) === false) {
		echo 'ERROR: Failed to write phpDocumentor configuration file.', PHP_EOL;
		exit(1);
	} else {
		echo 'SUCCESFULLY updated/created the phpdoc.xml file!', PHP_EOL;
	}

	exit(0);
};

$requests_phpdoc_version_updater();
