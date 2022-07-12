# Contributing

So you want to contribute to Requests? Fantastic! Here are a few guidelines to follow for all contributions.

## Process

1. Ideally, start with [opening an issue][new-issue] to check the need for a PR. It's possible that a feature may be rejected at an early stage, and it's better to find out before you write the code.
    Note: There may be an issue or PR open already. If so, please join the discussion in that issue or PR instead of opening a duplicate issue/PR.
2. Fork [the repository][] on Github, create a new branch off the `develop` branch and write the code.
    - Small, atomic commits are preferred.
    - Explain the motivation behind the change in the commit message and if relevant, provide links to external sources on which you based your change.
    - Please ensure any new features and bug fixes are accompanied by tests which fully cover the change.
3. File a PR and fill out the PR template. If the PR isn't ready for review/merge yet, open the PR [as a draft][draft-prs]. If your PR closes an existing issue, add "Fixes #xxx" to the message, so that the issue will be closed when the PR is merged.
4. If needed, iterate on the code until it is ready. When you're ready, mark the PR as "ready for review".
5. Automated checks will be run on all PRs via [GitHub Actions][ghactions]. Please fix any issues identified by these automated checks promptly. It is unlikely your PR will be reviewed until the automated checks pass.
6. A committer will review your code and offer you feedback.
7. Update with the feedback as necessary. This will take you back to step 5. Rinse and repeat step 5 - 7 as often as necessary.
8. PR will be merged.

Note:

* All code needs to go through peer review. Committers may not merge their own PR.

If you have questions while working on your contribution and you use Slack, there is
a [#core-http-api][] channel available in the [WordPress Slack][] in which contributions can be discussed.

[new-issue]: https://github.com/WordPress/Requests/issues/new/choose
[the repository]: https://github.com/WordPress/Requests
[draft-prs]: https://github.blog/2019-02-14-introducing-draft-pull-requests/
[ghactions]: https://github.com/WordPress/Requests/actions
[#core-http-api]: https://wordpress.slack.com/archives/C02BBE29V42
[WordPress Slack]: https://make.wordpress.org/chat/

## Compatibility

All code in Requests must be compatible with PHP 5.6 up to the latest stable PHP version.
Requests is used in WordPress, and hence is tied to [its compatibility][wp-requirements].

Requests uses [semantic versioning][semver].
This means that backward-compatibility (BC) breaks are ONLY allowed in major releases and should be clearly annotated in the changelog.

Whenever a BC break _is_ introduced, it is good practice to always consider if a (temporary) measure can be put in place to smooth out the BC break.
For example: don't just rename a function/method, but deprecate the old function and at the same time introduce the new function. This gives users of the library time to update their code and switch to the new function. Removing the deprecated function can then happen in the next major release.

Generally speaking, the goal of Requests is to provide an interface that smoothes the differences across various server setups.
That means there shouldn't be any code relying on any optional PHP extensions (even if they're common) unless we also provide a pure-PHP version (that is, we can use extensions to improve performance when available).
Only two extensions are currently used:
* [cURL][curl] is only used for better performance over sockets.
* [JSON][json] for decoding received responses.

[wp-requirements]: https://wordpress.org/about/requirements/
[semver]: https://semver.org/
[curl]: https://www.php.net/book.curl
[json]: https://www.php.net/book.json

## Linting

Code should (obviously) not contain any parse errors.
This project uses [PHP Parallel Lint][] to monitor this for all supported PHP versions.

The linter can be run locally using `composer lint`.

[PHP Parallel Lint]: https://github.com/php-parallel-lint/PHP-Parallel-Lint

## Coding Style

Please follow the existing coding style and best practices.
This project uses [PHP_CodeSniffer][] to detect coding standard violations and apply automated fixes (whenever possible).

* All files can be checked for coding standard violations by running `composer checkcs`.
* Any automatically applicable fixes can be applied by running `composer fixcs`.

[PHP_CodeSniffer]: https://github.com/squizlabs/PHP_CodeSniffer

## Unit Tests

PRs should include unit tests for all changes.

Tests for this library are written using [PHPUnit][] in combination with the [PHPUnit Polyfills][].
This means that tests can be written for the latest version of PHPUnit (9.x at the time of writing) and still be run on all PHPUnit versions needed to test all supported PHP versions (PHPUnit 5.x - 9.x).

Tests are organized as follows:
* Each class in the `src` directory has a corresponding subdirectory in the `tests` directory.
    Example: the tests for the `\WpOrg\Requests\Cookie` class in `src/Cookie.php` can be found in the `tests/Cookie/` subdirectory.
* Unit tests for a specific method will be in this subdirectory in a file/class matching the method name.
    Example: unit tests for the `\WpOrg\Requests\Cookie::domain_matches()` method are in the `WpOrg\Requests\Tests\Cookie\DomainMatchesTest` class in the `tests/Cookie/DomainMatchesTest.php` file.
* Integration tests for a class will be in this subdirectory in a file/class matching the class name.
    Example: integration tests for the `\WpOrg\Requests\Cookie` class are in the `WpOrg\Requests\Tests\Cookie\CookieTest` class in the `tests/Cookie/CookieTest.php` file.

If at all possible, features **must** be unit tested.
All tests should have `@covers` tags pointing to the specific code which is being tested.
Code coverage is monitored for every PR and for the code base as a whole using [CodeCov][].

[PHPUnit]: https://phpunit.readthedocs.io/en/main/
[PHPUnit Polyfills]: https://github.com/Yoast/PHPUnit-Polyfills/
[CodeCov]: https://app.codecov.io/gh/WordPress/Requests/branch/develop

### Prerequisites for running the tests

- [PHP][] >= 5.6
- [Composer][]
- [Python 3][]
- [mitmproxy][] (`pip3 install mitmproxy`)

[PHP]: https://www.php.net/
[Composer]: http://getcomposer.org/
[Python 3]: https://www.python.org/
[mitmproxy]: https://mitmproxy.org/

### Running the Tests

```bash
# Start the test server
PORT=8080 vendor/bin/start.sh
export "REQUESTS_TEST_HOST_HTTP=localhost:8080"

# Start the proxy server
PORT=9002 tests/utils/proxy/start.sh
PORT=9003 AUTH="test:pass" tests/utils/proxy/start.sh
export "REQUESTS_HTTP_PROXY=localhost:9002"
export "REQUESTS_HTTP_PROXY_AUTH=localhost:9003"
export "REQUESTS_HTTP_PROXY_AUTH_USER=test"
export "REQUESTS_HTTP_PROXY_AUTH_PASS=pass"

# Run the tests
composer test

# Stop the proxy server
PORT=9002 tests/utils/proxy/stop.sh
PORT=9003 tests/utils/proxy/stop.sh

# Stop the test server
vendor/bin/stop.sh
```

To run the test with code coverage, use `composer coverage` instead.

## Website

A PR to update the [website][] containing the documentation for Requests is automatically generated via GitHub Actions whenever a new release is tagged.

The website is a [GitHub Pages][] website, built up from the following sources:
* Templates, styling and a few markdown files in the `gh-pages` branch.
* The `README` file and the prose documentation in the `docs` directory of the `stable` branch, which are automatically converted for use in GH Pages using the scripts in the [`build/ghpages`][] directory.
* API documentation, which is based on the docblocks in the source code, generated using [phpDocumentor][].

[website]: https://requests.ryanmccue.info/
[GitHub Pages]: https://docs.github.com/en/pages
[`build/ghpages`]: https://github.com/WordPress/Requests/blob/develop/build/ghpages/README.md
[phpDocumentor]: https://www.phpdoc.org/

## Releases

There is a [release checklist][] available detailing the release workflow.

[release checklist]: https://github.com/WordPress/Requests/blob/develop/.github/release-checklist.md

## Licensing

By contributing code to this repository, you agree to license your code for use under the [ISC License][].

[ISC License]: https://github.com/rmccue/Requests/blob/stable/LICENSE
