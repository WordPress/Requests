#!/usr/bin/env php
<?php
/**
 * Update the markdown based documentation files.
 *
 * {@internal This functionality has a minimum PHP requirement of PHP 7.2.}
 *
 * @internal
 *
 * @package Requests
 * @subpackage GHPages
 */

namespace WpOrg\Requests\GHPages;

require_once __DIR__ . '/UpdateMarkdown.php';

$requests_website_updater        = new UpdateMarkdown();
$requests_website_update_success = $requests_website_updater->run();

exit($requests_website_update_success);
