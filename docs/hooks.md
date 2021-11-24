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

    Parameters: `string &$url`, `array &$headers`, `array|string &$data`,
    `string &$type`, `array &$options`

* **`requests.before_parse`**

    Alter the raw HTTP response before parsing.

    Parameters: `string &$response`

* **`requests.after_request`**

    Alter the response object before it is returned to the user.

    Parameters: `WpOrg\Requests\Response &$return`

* **`curl.before_request`**

    Set cURL options before the transport sets any (note that Requests may
    override these).

    Parameters: `cURL resource|CurlHandle &$fp`

* **`curl.before_send`**

    Set cURL options just before the request is actually sent via `curl_exec()`.

    Parameters: `cURL resource|CurlHandle &$fp`

* **`curl.after_request`**

    Alter the raw HTTP response before returning for parsing.

    Parameters: `string &$response, array &$info`

    `$info` contains the associated array as defined in the return value for [curl_getinfo()](https://www.php.net/curl-getinfo#refsect1-function.curl-getinfo-returnvalues).

* **`fsockopen.before_request`**

    Run events before the transport does anything.

* **`fsockopen.after_headers`**

    Add extra headers before the body begins (i.e. before `\r\n\r\n`).

    Parameters: `string &$out`

* **`fsockopen.before_send`**

    Add body data before sending the request.

    Parameters: `string &$out`

* **`fsockopen.after_send`**

   Run events after writing the data to the socket.

* **`fsockopen.after_request`**

    Alter the raw HTTP response before returning for parsing.

    Parameters: `string &$response, array &$info`

    `$info` contains the associated array as defined in the return value for [stream_get_meta_data()](https://www.php.net/stream-get-meta-data#refsect1-function.stream-get-meta-data-returnvalues).



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
