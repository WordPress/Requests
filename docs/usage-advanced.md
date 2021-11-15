Advanced Usage
==============

Session Handling
----------------
Making multiple requests to the same site with similar options can be a pain,
since you end up repeating yourself. The Session object can be used to set
default parameters for these.

Let's simulate communicating with GitHub.

```php
$session = new WpOrg\Requests\Session('https://api.github.com/');
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


Secure Requests with SSL
------------------------

It is recommended to always use secure requests whenever the server setup allows for it.

Setting the `$options['verify']` key to `true` when initiating a request enables certificate
verification using the certificate authority list provided by the server environment:

```php
// Use server-provided certificate authority list.
$options  = array('verify' => true);
$response = WpOrg\Requests\Requests::get('https://httpbin.org/', array(), $options);
```

The actual behavior depends on the transport being used, but in general should be based on the [`openssl` PHP ini settings].

If you're accessing sites with certificates from other certificate authorities (CAs), or self-signed certificates,
you can point Requests to a custom CA list in PEM form - the same format is accepted by both cURL and OpenSSL.
You can do so by using the `'verify'` option with a filepath string:

```php
// Use custom certificate authority list.
$options = array(
    'verify' => '/path/to/cacert.pem'
);
$response = WpOrg\Requests\Requests::get('https://httpbin.org/', array(), $options);
```

As a fallback, Requests bundles certificates from the [Mozilla certificate authority list],
which is the same list of root certificates used by most browsers.
This fallback is used when the `'verify'` option is not provided at all:

```php
// Use fallback certificate authority list.
$response = WpOrg\Requests\Requests::get('https://httpbin.org/');
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

$ssl_available = WpOrg\Requests\Requests::test(array(Capability::SSL => true));
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
