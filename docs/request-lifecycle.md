Lifecycle of a request
======================

This page explains what happens while Requests is making an HTTP request, how long that takes, and how it relates to the PHP script that started the request.
It answers questions such as "will my request still complete if the visitor closes their browser?".


Blocking requests
-----------------
By default, requests are blocking (`'blocking' => true`).
A call such as `WpOrg\Requests\Requests::get()` does not return until one of the following happens:

* The full response has been received.
  You get a `WpOrg\Requests\Response` object back.
* The `connect_timeout` (default: `10` seconds) is reached while connecting to the server, or the `timeout` (default: `10` seconds) is reached while waiting for the response.
  A `WpOrg\Requests\Exception` is thrown.
* Some other error occurs, such as a DNS failure or an SSL error.
  A `WpOrg\Requests\Exception` is thrown.

How the `timeout` is measured depends on the transport:

* The **cURL** transport uses it as the maximum time for the whole request.
* The **fsockopen** transport uses it as the maximum time to wait for the next piece of the response.
  A slow server which keeps sending data can therefore take longer than the `timeout` in total.

```php
$options  = array(
    'connect_timeout' => 5,
    'timeout'         => 30,
);
$response = \WpOrg\Requests\Requests::get('https://httpbin.org/delay/3', array(), $options);
```

Note that the cURL transport always uses a `timeout` of at least one second, even if a lower value is passed.
This is because cURL needs a timeout of at least one second when it uses the system DNS resolver.


Non-blocking requests
---------------------
If you only want to send a request and do not need the response, you can set `'blocking' => false`.

```php
$options = array(
    'blocking' => false,
    'timeout'  => 0.01,
);
\WpOrg\Requests\Requests::post('https://example.com/start-job', array(), array('job' => 42), $options);
```

For a non-blocking request:

* The returned `WpOrg\Requests\Response` object is empty: there is no status code, no headers and no body.
* Errors which happen after the request has been sent, such as a timeout, are not reported.
* The server which receives the request is not affected by Requests disconnecting.
  Whether that server keeps processing the request after the connection is closed depends entirely on how the server-side script is set up (see below).

How quickly the call returns depends on the transport:

* The **fsockopen** transport connects to the server, writes the request, closes the connection and returns straight away.
* The **cURL** transport still waits for the response, but does not process it.
  It returns once the response has been received or the `timeout` is reached, whichever comes first.
  As the cURL transport uses a minimum `timeout` of one second, a non-blocking request can take up to one second to return.

In both cases, connecting to the server (including the DNS lookup and, for HTTPS, the SSL handshake) still happens before the call returns.
A slow or unreachable server can therefore still delay a non-blocking request.


What happens when the visitor goes away?
----------------------------------------
Requests makes outgoing HTTP requests from within your PHP script.
It has no knowledge of the incoming connection between the visitor's browser and your script, so it neither checks for nor reacts to that visitor disconnecting.

Whether a request completes when the visitor closes their browser, therefore depends on whether PHP keeps running your script.
The [PHP manual on connection handling][php-connection-handling] describes this in detail, but the main points are:

* By default, PHP terminates a script when the visitor disconnects.
* However, PHP only notices that the visitor has disconnected when the script next tries to send output to the visitor.
  While your script is waiting for a blocking request to finish, it is not sending any output, so the request will normally run to completion.
  The script is then terminated the next time it sends output.
* If the script calls [`ignore_user_abort(true)`][php-ignore-user-abort], or the `ignore_user_abort` ini setting is enabled, PHP keeps running the script after the visitor disconnects.
  Use this when work after the request also needs to complete, such as saving the response.
* A script can still be stopped by the time limit set via [`set_time_limit()`][php-set-time-limit] or the `max_execution_time` ini setting, or by a timeout in the web server or the PHP-FPM configuration.
  Note that on most systems, time spent waiting on network streams does not count towards `max_execution_time`, but this is not the case on Windows.

For long-running work, you should not rely on a visitor's browser connection staying open.
Instead, start the work in a separate request which ignores user aborts.

[php-connection-handling]: https://www.php.net/manual/en/features.connection-handling.php
[php-ignore-user-abort]: https://www.php.net/manual/en/function.ignore-user-abort.php
[php-set-time-limit]: https://www.php.net/manual/en/function.set-time-limit.php


Example: how WordPress runs scheduled tasks
-------------------------------------------
WordPress uses this pattern to run its scheduled tasks ("WP-Cron").

1. When a page is loaded and a scheduled task is due, WordPress sends a non-blocking POST request with a `timeout` of `0.01` seconds to its own `wp-cron.php` file, using `wp_remote_post()`, which uses Requests.
2. The page load carries on and finishes as normal.
   With the cURL transport, it can be delayed by up to one second (see [Non-blocking requests](#non-blocking-requests)).
3. `wp-cron.php` runs as a separate PHP request.
   It calls `ignore_user_abort(true)` at the start, so it keeps running after the connection from the first request has been closed.

So the scheduled tasks are not tied to the visitor's browser: they keep running even if the visitor closes the tab.
They can still be stopped by the time limits of the server which runs `wp-cron.php`.

***

Previous: [Hooking system](hooks.md)

Next: [Upgrading to Requests 2.0](upgrading.md)
