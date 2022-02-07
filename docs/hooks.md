Hooking system
==============

Requests has a hook system that you can use to manipulate parts of the request
process along with internal transport hooks.

Check out the [API documentation for `WpOrg\Requests\Hooks`][requests_hooks] for more
information on how to use the hook system.

[requests_hooks]: {{ '/api-2.x/classes/WpOrg-Requests-Hooks.html' | prepend: site.baseurl }}

Available Hooks
---------------

* **`requests.before_request`**

    Alter the request before it is sent to the transport.

    Parameters: `string &$url`, `array &$headers`, `array|null &$data`,
    `string &$type`, `array &$options`

* **`request.progress`**

    Run events based on how much body data has been received up to this point for the current request.

    Parameters: `string $data`, `int $response_bytes`, `int|bool $response_byte_limit`

* **`requests.before_parse`**

    Alter the raw HTTP response before parsing.

    Parameters: `string &$response`, `string $url`, `array $headers`,
    `array|null $data`, `string $type`, `array $options`

* **`requests.before_redirect_check`**

    Alter the response object before a potential redirect is detected and acted on.

    Parameters: `WpOrg\Requests\Response &$return`, `array $req_headers`,
    `array $req_data`, `array $options`

* **`requests.before_redirect`**

    Alter the request arguments before a redirect is acted upon.

    Parameters: `string &$location`, `array &$req_headers`, `array &$req_data`,
    `array &$options`, `WpOrg\Requests\Response $return`

* **`requests.after_request`**

    Alter the response object before it is returned to the user.

    Parameters: `WpOrg\Requests\Response &$return`, `array &$req_headers`,
    `array &$req_data`, `array &$options`

* **`multiple.request.complete`**

    Alter the response for an individual request in a multi-request.

    Parameters: `WpOrg\Requests\Response &$response`, `string|int $id`

    Do not register this hook directly.
    To use this hook, pass a callback via `$options['complete']` when calling
    `WpOrg\Requests\Requests\request_multiple()`.

* **`curl.before_request`**

    Set cURL options before the transport sets any (note that Requests may
    override these).

    Parameters: `cURL resource|CurlHandle &$handle`

* **`curl.before_send`**

    Set cURL options just before the request is actually sent via `curl_exec()`.

    Parameters: `cURL resource|CurlHandle &$handle`

* **`curl.after_send`**

    Run events after sending the request, but before interpreting the response.

    Parameters: -

* **`curl.after_request`**

    Alter the raw HTTP response before returning for parsing.

    Parameters: `string &$headers`, `array &$info`

    The `$info` parameter contains the associated array as defined in
    the return value for [curl_getinfo()](https://www.php.net/curl-getinfo#refsect1-function.curl-getinfo-returnvalues).

    When a non-blocking request was made (`$options['blocking'] = false`), both the `$headers` string as well as the `$info` array will be empty.

    Prior to Requests 2.1.0, the `$info` parameter was omitted for non-blocking requests.

* **`curl.before_multi_add`**

    Set cURL options before adding the request to a cURL multi handle.

    Parameters: `cURL resource|CurlHandle &$subhandle`

* **`curl.before_multi_exec`**

    Set cURL options before executing a cURL multi handle request.

    Parameters: `cURL resource|CurlMultiHandle &$multihandle`

* **`curl.after_multi_exec`**

    Run events before a cURL multi handle is closed.

    Parameters: `cURL resource|CurlMultiHandle &$multihandle`

* **`fsockopen.before_request`**

    Run events before the transport does anything.

* **`fsockopen.remote_socket`**

    Alter the remote socket.

    Parameters: `string &$remote_socket`

* **`fsockopen.remote_host_path`**

    Alter the remote host path.

    Parameters: `string &$path`, `string $url`

* **`fsockopen.after_headers`**

    Add extra headers before the body begins (i.e. before `\r\n\r\n`).

    Parameters: `string &$out`

* **`fsockopen.before_send`**

    Add body data before sending the request.

    Parameters: `string &$out`

* **`fsockopen.after_send`**

    Run events after writing the data to the socket.

    Parameters: `string $out`

* **`fsockopen.after_request`**

    Alter the raw HTTP response before returning for parsing.

    Parameters: `string &$headers`, `array &$info`

    The optional `$info` parameter contains the associated array as defined
    in the return value for [stream_get_meta_data()](https://www.php.net/stream-get-meta-data#refsect1-function.stream-get-meta-data-returnvalues).

    When a non-blocking request was made (`$options['blocking'] = false`), both the `$headers` string as well as the `$info` array will be empty.

    Prior to Requests 2.1.0, the `$info` parameter was omitted for non-blocking requests.


Registering Hooks
-----------------
Note: if you're doing this in an authentication handler, see the [Custom
Authentication guide][authentication-custom] instead.

[authentication-custom]: authentication-custom.md

In order to register your own hooks, you need to instantiate `WpOrg\Requests\Hooks`
and pass the object in via the `'hooks'` option.

```php
$hooks = new WpOrg\Requests\Hooks();
$hooks->register('requests.after_request', 'mycallback');

$request = WpOrg\Requests\Requests::get('https://httpbin.org/get', array(), array('hooks' => $hooks));
```

***

Previous: [Requests through proxy](proxy.md)

Next: [Upgrading to Requests 2.0](upgrading.md)
