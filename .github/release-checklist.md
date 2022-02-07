Template to use for release PRs from `develop` to `stable`
===========================================================

:warning: **DO NOT MERGE (YET)** :warning:

PR for tracking changes for the x.x.x release.

Target release date: **DOW MONTH DAY YEAR**.

- [ ] Check if any dependencies need updating.
- [ ] Update the version constant in `src/Requests.php` - PR #xxx.
- [ ] Add changelog for the release - PR #xxx
- [ ] Merge this PR.
- [ ] Make sure all CI builds are green.
- [ ] Tag the release against `stable` and push the tag.
- [ ] Review the automatically created PR with the GH Pages docs update.
- [ ] Create a release from the tag (careful, GH defaults to `develop`!) & copy & paste the changelog to it.
    Make sure to copy the links to the issues and the links to the GH usernames from the bottom of the changelog!
- [ ] Merge the GH Pages PR.
    Note: it is important to do this **after** the release as otherwise the information about the latest release
    in the site will not be updated correctly from the GitHub API.
- [ ] Verify that the website regenerated correctly and is in working order.
- [ ] Close the milestone.
- [ ] Open a new milestone for the next release.
- [ ] If any open PRs/issues which were milestoned for the release did not make it into the release, update their milestone.
- [ ] Tweet about the release.
- [ ] Post about it in the WP #core Slack channel.
- [ ] Open a Trac ticket for WordPress Core to update their copy.
- [ ] Submit for "Month in WordPress": https://make.wordpress.org/community/month-in-wordpress-submissions/
