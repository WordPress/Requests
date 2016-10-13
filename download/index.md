---
layout: page
title: Download
---
Download
========

The current version of Requests is 1.7, which you can
download from GitHub as a
[zip](https://github.com/rmccue/Requests/archive/v1.7.0.zip) or a
[tarball](https://github.com/rmccue/Requests/archive/v1.7.0.tar.gz).

You can also check out the [releases page][releases] on GitHub for a changelog
plus more information.

[releases]: https://github.com/rmccue/Requests/releases


Alternative Methods
-------------------

### Installing via Composer
If you're using [Composer](https://github.com/composer/composer) to manage
dependencies, you can add Requests with it.

{% highlight json %}
{
    "require": {
        "rmccue/requests": ">=1.0"
    },
    "autoload": {
        "psr-0": {"Requests": "library/"}
    }
}
{% endhighlight %}

### Installing via Git
To install the source code:

{% highlight sh %}
$ git clone git://github.com/rmccue/Requests.git
{% endhighlight %}

And include it in your scripts:

{% highlight php startinline %}
require_once '/path/to/Requests/library/Requests.php';
{% endhighlight %}

You'll probably also want to register an autoloader:

{% highlight php startinline %}
Requests::register_autoloader();
{% endhighlight %}


Previous Versions
-----------------

* Version 1.6: [Zip](https://github.com/rmccue/Requests/archive/v1.6.0.zip) or
  [Tarball](https://github.com/rmccue/Requests/archive/v1.6.0.tar.gz)

* Version 1.5: [Zip](https://github.com/rmccue/Requests/archive/v1.5.zip) or
  [Tarball](https://github.com/rmccue/Requests/archive/v1.5.tar.gz)
