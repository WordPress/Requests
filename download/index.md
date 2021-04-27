---
layout: page
title: Download
---
Download
========

The current version of Requests is {{ site.github.latest_release.tag_name }}, which you can
download from GitHub as a
[zip]({{ site.github.latest_release.zipball_url }}) or a
[tarball]({{ site.github.latest_release.tarball_url }}).

You can also check out the [releases page][releases] on GitHub for a changelog
plus more information.

[releases]: {{ site.github.releases_url }}


Alternative Methods
-------------------

### Installing via Composer
If you're using [Composer](https://getcomposer.org/) to manage
dependencies, you can add Requests with it.

```json
{
    "require": {
        "{{ site.requests.packagist }}": "^{{ site.github.latest_release.tag_name | replace_first: 'v', '' }}"
    },
    "autoload": {
        "psr-0": {"Requests": "library/"}
    }
}
```

### Installing via Git
To install the source code:

```bash
$ git clone {{ site.github.clone_url }}
```

And include it in your scripts:

```php
require_once '/path/to/Requests/library/Requests.php';
```

You'll probably also want to register an autoloader:

```php
Requests::register_autoloader();
```


Previous Versions
-----------------

{%- assign ghreleases = site.github.releases | where: "draft", false | sort: 'tag_name' | reverse -%}
{% for release in ghreleases %}
    {% if release.tag_name != site.github.latest_release.tag_name %}
* Version {{ release.tag_name }}: [Zip]({{ release.zipball_url}}) or [Tarball]({{ release.tarball_url}})
    {%- endif -%}
{%- endfor -%}
