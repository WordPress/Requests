# Contributing

So you want to contribute to Requests? Fantastic! There are a few rules you'll need to follow for all contributions.

(There are always exceptions to these rules. :) )

## Process

1. Ideally, start with an issue to check the need for a PR. It's possible that a feature may be rejected at an early stage, and it's better to find out before you write the code.
2. Write the code. Small, atomic commits are preferred. Explain the motivation behind the change when needed.
3. File a PR. If it isn't ready for merge yet, note that in the description. If your PR closes an existing issue, add "fixes #xxx" to the message, so that the issue will be closed when the PR is merged.
4. If needed, iterate on the code until it is ready. This includes adding unit tests. When you're ready, comment that the PR is complete.
5. A committer will review your code and offer you feedback.
6. Update with the feedback as necessary.
7. PR will be merged.

Notes:

* All code needs to go through peer review. Committers may not merge their own PR.
* PRs should **never be squashed or rebased**, even when merging. Keeping the history is important for tracking motivation behind changes later.

## Compatibility

All code in Requests must be compatible with PHP 5.6.
Requests is used in WordPress, and hence is tied to [its compatibility][wp-requirements].

Requests is also bound to not break backwards compatibility.
In semantic versioning terms, this means we will be in the 2.x release cycle for the forseeable future.

Generally speaking, the goal of Requests is to provide an interface that smoothes the differences across various server setups.
That means there shouldn't be any code relying on any extensions (even if they're common) unless we also provide a pure-PHP version (that is, we can use extensions to improve performance when available). Only two extensions are currently used: SPL, and cURL.
SPL is always available from PHP 5.3 onwards.
cURL is only used for better performance over sockets.

[wp-requirements]: https://wordpress.org/about/requirements/


## Coding Style

Where possible, follow the existing style. That looks something like this:

```php
/**
 * Class that does a thing.
 */
class Requests_Some_Class_Name {
	/**
	 * Do a thing.
	 *
	 * There might be a longer description here too. All sentences and phrases
	 * should be ended with a period. Wrap at 80 characters.
	 *
	 * The short description should be in the imperative form (as a command).
	 *
	 * @param boolean $with Param docs.
	 * @param array $args Further docs.
	 * @return boolean Description if required. If no value is returned, omit.
	 */
	public function some_method($with, $args) {
		if ($with) {
			do_something($with, $args);
		}
		if (!$args || $with === $args) {
			do_another_thing();
		}
	}
}
```


## Unit Tests

PRs should include unit tests for any changes.
These are written in PHPUnit, and should be added to the file corresponding to the class they test (that is, tests for `library/Requests/Cookie.php` would be in `tests/Cookie.php`).

Where possible, features must be unit tested.
We aim for >90% coverage at all times.
The master branch may drop below 90% if features are merged independently of their tests, but there is a hard limit of 85%. Release versions must have >90% coverage.

For complex features by third-parties, PRs may be merged that drop coverage below the 90% threshold, with the intent of increasing tests back up in a subsequent PR.


## Licensing

By contributing code to this repository, you agree to license your code for use under the [ISC License](https://github.com/rmccue/Requests/blob/master/LICENSE).
