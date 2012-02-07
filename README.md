Requests for PHP
================

Requests is a HTTP library written in PHP, for human beings. It is roughly
based on the API from the excellent [Requests Python
library](http://python-requests.org/). Requests is [ISC
Licensed](https://github.com/rmccue/Requests/blob/master/LICENSE) (similar to
the new BSD license) and has no dependencies.

Despite PHP's use as a language for the web, its tools for sending HTTP requests
are severely lacking. cURL has an
[interesting API](http://php.net/manual/en/function.curl-setopt.php), to say the
least, and you can't always rely on it being available. Sockets provide only low
level access, and require you to build most of the HTTP response parsing
yourself.

We all have better things to do. That's why Requests was born.

```php
$headers = array('Accept' => 'application/json');
$options = array('auth' => array('user', 'pass'));
$request = Requests::get('https://api.github.com/gists', $headers, $options);

var_dump($request->status_code);
// int(200)

var_dump($request->headers['content-type']);
// string(31) "application/json; charset=utf-8"

var_dump($request->body);
// string(26891) "[...]"
```

Requests allows you to send  **HEAD**, **GET** and **POST** HTTP requests. You
can add headers, form data, multipart files, and parameters with simple arrays,
and access the response data in the same way. Requests uses cURL and fsockopen,
depending on what your system has available, but abstracts all the nasty stuff
out of your way, providing a consistent API.


Features
--------

- International Domains and URLs
- Browser-style SSL Verification
- Basic/Digest Authentication
- Automatic Decompression
- Connection Timeouts


Installation
------------

If you want to include it with your project, either [grab it as a zip](https://github.com/rmccue/Requests/zipball/master) or grab it as a tarball:

    $ curl https://github.com/rmccue/Requests/tarball/master | tar xzv
    (or)
    $ wget https://github.com/rmccue/Requests/tarball/master -O - | tar xzv

Or clone it via Git:

    $ git clone https://github.com/rmccue/Requests.git


Documentation
-------------
The best place to start is looking at [the documentation for
`Requests::request()`](http://requests.ryanmccue.info/api/class-Requests.html#_request),
where all the parameters are fully documented.

Requests is [100% documented with PHPDoc](http://requests.ryanmccue.info/api/).
If you find any problems with it, [create a new
issue](https://github.com/rmccue/Requests/issues/new)!


Testing
-------
Requests strives to have 100% code-coverage of the library with an extensive
set of tests. We're not quite there yet, but [we're getting
close](http://requests.ryanmccue.info/coverage/).

To run the test suite, simply:

    $ cd tests
    $ phpunit

If you'd like to run a single set of tests, specify just the name:

    $ phpunit Transport/cURL

Contribute
----------

1. Check for open issues or open a new issue for a feature request or a bug
2. Fork [the repository][] on Github to start making your changes to the
    `master` branch (or branch off of it)
3. Write a test which shows that the bug was fixed or that the feature works as expected
4. Send a pull request and bug me until I merge it

[the repository]: https://github.com/rmccue/Requests
