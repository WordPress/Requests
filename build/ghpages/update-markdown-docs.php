#!/usr/bin/env php
<?php
/**
 * Requests for PHP, an HTTP library.
 *
 * Update the markdown based documentation files.
 *
 * {@internal This functionality has a minimum PHP requirement of PHP 7.2.}
 *
 * @internal
 *
 * @package   Requests\GHPages
 * @copyright 2012-2023 Requests Contributors
 * @license   https://github.com/WordPress/Requests/blob/stable/LICENSE ISC
 * @link      https://github.com/WordPress/Requests
 */

namespace WpOrg\Requests\GHPages;

require_once __DIR__ . '/UpdateMarkdown.php';

$requests_website_updater        = new UpdateMarkdown();
$requests_website_update_success = $requests_website_updater->run();

exit($requests_website_update_success);
