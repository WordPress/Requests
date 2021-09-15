Scripts to update the GitHub Pages website
======================================

The scripts in this directory are only for internal use to update the GitHub Pages website associated with this project whenever a new version of the Requests library is released.

They are used in the [`update-website.yml`](https://github.com/WordPress/Requests/blob/develop/.github/workflows/update-website.yml) GitHub Actions workflow.

To run a test build of the GitHub Pages site locally, execute the following steps:

Preparation in this repo:
* Pre-requisite: use PHP 7.2 or higher.
* From within this subdirectory, run `composer update -W`.
* Delete the `build/ghpages/artifacts` subdirectory completely.

Preparation of the GitHub Pages branch:
* Clone this repo a second time outside of the root of this clone and check out the `gh-pages` branch.
* Create a new branch (git).
* Delete the `api` directory completely.
* Delete the `docs` directory completely.

Switch to the project root directory in this clone and:
* Run `php build/ghpages/update-docgen-config.php` to retrieve the latest tag number from the GH API and create/update the `phpdoc.xml` config.
* If this was the first time you ran the above script, you now need to edit the `phpdoc.xml` file and update the path in the `<paths>` - `<output>` config to point to the `root/api` directory of the "gh-pages" clone of the repo.
* Run `php build/ghpages/vendor/bin/phpdoc` to generate the API docs.
* Run `php build/ghpages/update-markdown-docs.php --target=path/to/gh-pages/root` to generate versions of the markdown docs suitable for use in GH Pages.

You can then use git diff to verify the GH Pages site was updated correctly.
