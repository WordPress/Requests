---
layout: documentation
title: Download
---
Download
========

The current version of Requests is 1.5, which you can
[download from GitHub](https://github.com/rmccue/Requests/zipball/v1.5) as a
zip.


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
    },
    "minimum-stability": "dev"
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
