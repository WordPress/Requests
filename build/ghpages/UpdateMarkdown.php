<?php
/**
 * Update the markdown based documentation files.
 *
 * {@internal This functionality has a minimum PHP requirement of PHP 7.2.}
 *
 * @internal
 *
 * @package Requests\GHPages
 *
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewParamTypeDeclarations.stringFound
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewReturnTypeDeclarations.intFound
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewReturnTypeDeclarations.stringFound
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewReturnTypeDeclarations.voidFound
 * @phpcs:disable PHPCompatibility.FunctionUse.NewFunctionParameters.dirname_levelsFound
 */

namespace WpOrg\Requests\GHPages;

use RuntimeException;

class UpdateMarkdown {

	/**
	 * Target directory for the updated/transformed files.
	 *
	 * @var string
	 */
	private $target = __DIR__ . '/artifacts';

	/**
	 * Frontmatter for the website homepage.
	 *
	 * @var string
	 */
	private $home_frontmatter = '---
layout: home
title:
---
';

	/**
	 * Frontmatter template for files in the docs directory.
	 *
	 * @var string
	 */
	private $docs_frontmatter = '---
layout: documentation
title: %s
---
';

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->process_cli_args();
	}

	/**
	 * Process received CLI arguments.
	 *
	 * Only one argument is supported: "--target" to set the target path.
	 *
	 * @return void
	 */
	private function process_cli_args(): void {
		$args = $_SERVER['argv'];

		// Remove the call to the script itself.
		\array_shift($args);

		if (empty($args)) {
			// No options set.
			return;
		}

		foreach ($args as $arg) {
			preg_match('`--target=([\'"])?([^\'"]+)\1?`', $arg, $matches);
			if (empty($matches) || isset($matches[2]) === false) {
				// Not a valid CLI argument, only "target" is supported.
				continue;
			}

			$cwd    = getcwd();
			$target = $matches[2];

			/*
			 * Attempt some minimal path resolving.
			 * Note: the target directory may not exist, so just guestimating for most common cases here.
			 */
			if (strpos($target, '..') !== 0 && $target[0] === '.') {
				$this->target = $cwd . substr($target, 1);
				break;
			}

			if (strpos($target, '..') === 0) {
				while (strpos($target, '../') === 0 || strpos($target, '..\\') === 0) {
					$cwd    = dirname($cwd);
					$target = substr($target, 3);
				}

				$this->target = $cwd . '/' . $target;
				break;
			}

			/*
			 * In all other cases, presume it is a valid absolute path.
			 * The `put_contents()` method will throw appropriate errors if it's not.
			 */
			$this->target = $target;
		}
	}

	/**
	 * Run the transformation.
	 *
	 * @return int Exit code.
	 */
	public function run(): int {
		$exitcode = 0;

		try {
			$this->update_homepage();
			$this->update_docs();
		} catch (RuntimeException $e) {
			echo 'ERROR: ', $e->getMessage(), PHP_EOL;
			$exitcode = 1;
		}

		return $exitcode;
	}

	/**
	 * Transform the repo README to the website homepage.
	 *
	 * @return void
	 */
	private function update_homepage(): void {
		// Read the file.
		$contents = $this->get_contents(dirname(__DIR__, 2) . '/README.md');

		// Remove badges.
		$contents = preg_replace(
			'`([=]+[\n\r]+)(?:\[!\[[^\]]+\]\([^\)]+\)\]\([^\)]+\)[\n\r]+)+`',
			'$1',
			$contents
		);

		// Replace repo refs with GH Pages automatic replacement syntax.
		$contents = preg_replace(
			'`\brmccue/requests\b`',
			'{{ site.requests.packagist }}',
			$contents
		);

		// Replace version nr refs with GH Pages GH API automatic replacement syntax.
		$contents = preg_replace(
			'`"(?:>=1\.0|\^2\.0)"`',
			'"^{{ site.github.latest_release.tag_name | replace_first: \'v\', \'\' }}"',
			$contents
		);

		// Replace clone url refs with GH Pages GH API automatic replacement syntax.
		$contents = str_replace(
			'$ git clone git://github.com/WordPress/Requests.git',
			'$ git clone {{ site.github.clone_url }}',
			$contents
		);

		// Replace prose-based documentation link.
		$contents = preg_replace(
			'`\[prose-based documentation\]:[^\r\n]+`',
			'[prose-based documentation]: {{ \'/docs/\' | prepend: site.baseurl }}',
			$contents
		);

		// Update links.
		$contents = $this->update_links($contents);

		// Add frontmatter.
		$contents = $this->home_frontmatter . "\n" . $contents;

		// Write the file.
		$target = $this->target . '/index.md';
		$this->put_contents($target, $contents, 'doc index file');
	}

	/**
	 * Transform all docs in the `docs` directory for use in GH Pages.
	 *
	 * @return void
	 *
	 * @throws \RuntimeException When no markdown files are found in the docs directory.
	 */
	private function update_docs(): void {
		// Create the file list.
		$sep       = \DIRECTORY_SEPARATOR;
		$pattern   = dirname(__DIR__, 2) . $sep . 'docs' . $sep . '*.md';
		$file_list = glob($pattern, GLOB_NOESCAPE);

		if (empty($file_list)) {
			throw new RuntimeException('Failed to find doc files.');
		}

		$base_target = $this->target . '/docs/';

		foreach ($file_list as $file) {
			$basename = basename($file);

			if ($basename === 'README.md') {
				$this->update_docs_navigation($file);
			} else {
				$target = $base_target . $basename;
				$this->update_doc($file, $target);
			}
		}
	}

	/**
	 * Transform a "normal" docs markdown document for use in GHPages.
	 *
	 * @param string $source Path to the source file.
	 * @param string $target Path to the output file.
	 *
	 * @return void
	 *
	 * @throws \RuntimeException When the page title could not be found in the contents of a markdown file.
	 */
	private function update_doc(string $source, string $target): void {
		// Read the file.
		$contents = $this->get_contents($source);

		// Grab the title.
		$title = $this->get_title_from_contents($contents);
		if (!$title) {
			throw new RuntimeException(sprintf('Failed to find page title in doc file: %s', $source));
		}

		// Update links.
		$contents = $this->update_links($contents);

		// Add the frontmatter.
		$contents = sprintf($this->docs_frontmatter, $title) . "\n" . $contents;

		// Write the file.
		$this->put_contents($target, $contents);
	}

	/**
	 * Transform the index page of the docs directory into two separate files for use in GHPages.
	 *
	 * @param string $source Path to the source file.
	 *
	 * @return void
	 *
	 * @throws \RuntimeException When index page could not be split correctly into index and navigation.
	 */
	private function update_docs_navigation(string $source): void {
		// Read the file.
		$contents = $this->get_contents($source);

		// Update links.
		$contents = $this->update_links($contents);

		// Split the file.
		$parts = explode('<!-- Splitter DO NOT REMOVE Splitter -->', $contents);

		if (count($parts) !== 2) {
			throw new RuntimeException(sprintf('Failed to split the docs index file: %s', $source));
		}

		/*
		 * Create the docs index file.
		 */
		$docs_index = trim($parts[0]);

		// Grab the title.
		$title = $this->get_title_from_contents($contents);

		// Add the frontmatter.
		$docs_index = sprintf($this->docs_frontmatter, $title) . "\n" . $docs_index;

		// Add the navigation include.
		$docs_index .= "\n\n" . '{% include navigation.md %}';

		// Write the file.
		$target = $this->target . '/docs/index.md';
		$this->put_contents($target, $docs_index, 'doc index file');

		/*
		 * Create the docs navigation file.
		 */
		$navigation = trim($parts[1]);

		// Write the file.
		$target = $this->target . '/_includes/navigation.md';
		$this->put_contents($target, $navigation, 'navigation file');
	}

	/**
	 * Retrieve the contents of a file.
	 *
	 * @param string $source Path to the source file.
	 *
	 * @return string
	 *
	 * @throws \RuntimeException When the contents of the file could not be retrieved.
	 */
	private function get_contents(string $source): string {
		$contents = file_get_contents($source);
		if (!$contents) {
			throw new RuntimeException(sprintf('Failed to read doc file: %s', $source));
		}

		return $contents;
	}

	/**
	 * Write a string to a file.
	 *
	 * @param string $target   Path to the target file.
	 * @param string $contents File contents to write.
	 * @param string $type     Type of file to use in error message.
	 *
	 * @return string
	 *
	 * @throws \RuntimeException When the target directory could not be created.
	 * @throws \RuntimeException When the file could not be written to the target directory.
	 */
	private function put_contents(string $target, string $contents, string $type = 'doc file'): void {
		// Check if the target directory exists and if not, create it.
		$target_dir = dirname($target);

		// phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged -- Silencing warnings when function fails.
		if (@is_dir($target_dir) === false) {
			if (@mkdir($target_dir, 0777, true) === false) {
				throw new RuntimeException(sprintf('Failed to create the %s directory.', $target_dir));
			}
		} // phpcs:enable WordPress

		// Make sure the file always ends on a new line.
		$contents = rtrim($contents) . "\n";
		if (file_put_contents($target, $contents) === false) {
			throw new RuntimeException(sprintf('Failed to write %s to target location: %s', $type, $target));
		}
	}

	/**
	 * Retrieve the page title from the content of a markdown file.
	 *
	 * @param string $contents Contents of a markdown file.
	 *
	 * @return string
	 */
	private function get_title_from_contents(string $contents): string {
		return trim(substr($contents, 0, (strpos($contents, '===') - 1)));
	}

	/**
	 * Update links in the contents of a markdown file to make them usable in the context of GHPages.
	 *
	 * @param string $contents Markdown document contents.
	 *
	 * @return string
	 */
	private function update_links(string $contents): string {
		$contents = str_ireplace('README.md', 'index.md', $contents);
		$contents = preg_replace('`\b(\S+)\.md\b`i', '$1.html', $contents);

		return $contents;
	}
}
