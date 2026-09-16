Advanced Usage
==============

Session Handling
----------------
Making multiple requests to the same site with similar options can be a pain,
since you end up repeating yourself. The Session object can be used to set
default parameters for these.

Let's simulate communicating with GitHub.

```php
$session = new \WpOrg\Requests\Session('https://api.github.com/');
$session->headers['X-ContactAuthor'] = 'rmccue';
$session->useragent = 'My-Awesome-App';

$response = $session->get('/zen');
```

You can use the `url`, `headers`, `data` and `options` properties of the `WpOrg\Requests\Session`
object to set the defaults for this session, and the constructor also takes
parameters in the same order as `WpOrg\Requests\Requests::request()`. Accessing any other
properties will set the corresponding key in the options array; that is:

```php
// Setting the property...
$session->useragent = 'My-Awesome-App';

// ...is the same as setting the option
$session->options['useragent'] = 'My-Awesome-App';
```

## Streaming Responses

By default, Requests buffers the whole response body in memory before returning.
For large downloads or long-lived responses, such as Server-Sent Events from an
AI API, set the `stream` option to `true` instead. The request then returns as
soon as the response headers are in, and you read the body yourself:

```php
$response = \WpOrg\Requests\Requests::get('https://example.org/events', array(), array('stream' => true));

// The status code and headers are available right away.
var_dump($response->status_code);

// Pull the body off the wire in chunks.
while (!$response->eof()) {
  $chunk = $response->read();
  // do something with $chunk
}

// Release the connection when done.
$response->close();
```

The streaming API on `WpOrg\Requests\Response` is small:

- `read($length = 8192)` reads up to `$length` bytes of the body, blocking until
  data arrives. An empty string means the body has ended.
- `eof()` tells you whether the body has been fully read.
- `close()` releases the underlying connection. Also safe to call on responses
  that are not streamed.
- `is_streaming()` tells you whether the response is being streamed. Custom
  transports without streaming support fall back to a normal buffered response,
  which this lets you detect.

For line-based protocols such as NDJSON or Server-Sent Events, buffer the chunks
until a newline shows up. See
[`examples/stream.php`](https://github.com/WordPress/Requests/blob/develop/examples/stream.php)
for a runnable example.

A few things change when streaming:

- `$response->body` stays empty and `$response->raw` only contains the headers.
  The body comes out of `read()` instead.
- Bytes from `read()` are already de-chunked. Both transports ask the server for
  an uncompressed (`identity`) body, and Requests never decompresses streamed
  bytes, so what the server sends is what you get.
- The `timeout` option turns into an idle timeout: the maximum wait for the next
  chunk of data. A long-lived stream stays open as long as data keeps arriving.
- `max_bytes` and redirects work as usual. Redirect responses along the way are
  discarded; only the final response body is streamed.
- The `request.progress` hook fires once per `read()` call.
- `stream` needs a blocking request, and cannot be combined with `filename` or
  with `WpOrg\Requests\Requests::request_multiple()`.

Requests does not implement PSR-7, but `read()`, `eof()` and `close()` match the
semantics of the corresponding PSR-7 `StreamInterface` methods, so a [PSR-18
adapter][psr18-adapter] can wrap a streamed body as a readable PSR-7 stream
without translation.

[psr18-adapter]: https://github.com/Art4/requests-psr18-adapter


Secure Requests with SSL
------------------------

It is recommended to always use secure requests whenever the server setup allows for it.

Setting the `$options['verify']` key to `true` when initiating a request enables certificate
verification using the certificate authority list provided by the server environment:

```php
// Use server-provided certificate authority list.
$options  = array('verify' => true);
$response = \WpOrg\Requests\Requests::get('https://httpbin.org/', array(), $options);
```

The actual behavior depends on the transport being used, but in general should be based on the [`openssl` PHP ini settings].

If you're accessing sites with certificates from other certificate authorities (CAs), or self-signed certificates,
you can point Requests to a custom CA list in PEM form (the same format accepted by cURL and OpenSSL).
You can do so by using the `'verify'` option with a filepath string:

```php
// Use custom certificate authority list.
$options = array(
    'verify' => '/path/to/cacert.pem'
);
$response = \WpOrg\Requests\Requests::get('https://httpbin.org/', array(), $options);
```

As a fallback, Requests bundles certificates from the [Mozilla certificate authority list],
which is the same list of root certificates used by most browsers.
This fallback is used when the `'verify'` option is not provided at all:

```php
// Use fallback certificate authority list.
$response = \WpOrg\Requests\Requests::get('https://httpbin.org/');
```

:warning: **_Note however that this fallback should only be used for servers that are not properly
configured for SSL verification, as a continuously managed server should provide a more
up-to-date certificate authority list than a software library which only gets updates once in a while._**

If you want to disable verification completely, this is possible with `'verify' => false`,
but doing so is at your own risk as this is extremely insecure and should be avoided.

SSL verification might not be available depending on what extensions
are enabled for your PHP environment. You can test whether Requests has
access to a transport with SSL capabilities with the following call:

```php
use WpOrg\Requests\Capability;
use WpOrg\Requests\Requests;

$ssl_available = Requests::test(array(Capability::SSL => true));
```

### Security Note
Requests supports SSL across both cURL and fsockopen in a transparent manner.
Unlike other PHP HTTP libraries, support for verifying the certificate name is
built-in; that is, a request for `https://github.com/` will actually verify the
certificate's name even with the fsockopen transport. Requests was the
first PHP HTTP library to fully support SSL verification.

See also the [related PHP][php-bug-47030] and [OpenSSL-related][php-bug-55820]
bugs in PHP for more information on Subject Alternate Name field.

[Mozilla certificate authority list]: https://www.mozilla.org/projects/security/certs/
[`openssl` PHP ini settings]: https://www.php.net/manual/en/openssl.configuration.php
[php-bug-47030]: https://php.net/47030
[php-bug-55820]: https://php.net/55820

***

Previous: [Making a request](usage.md)

Next: [Authenticating your request](authentication.md)
