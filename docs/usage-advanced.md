Advanced Usage
==============

Secure Requests with SSL
------------------------
By default, HTTPS requests will use the most secure options available:

```php
$response = Requests::get('https://httpbin.org/');
```

Requests bundles certificates from the [Mozilla certificate authority list][],
which is the same list of root certificates used in most browsers. If you're
accessing sites with certificates from other CAs, or self-signed certificates,
you can point Requests to a custom CA list in PEM form (the same format
accepted by cURL and OpenSSL):

```php
$options = array(
	'verify' => '/path/to/cacert.pem'
);
$response = Requests::get('https://httpbin.org/', array(), $options);
```

Alternatively, if you want to disable verification completely, this is possible
with `'verify' => false`, but note that this is extremely insecure and should be
avoided.

## Compatibility Note
Requests supports SSL across both cURL and fsockopen in a transparent manner.
Using fsockopen is slightly less secure, as the common name in certificates is
not checked. This is due to
[PHP lacking support for Subject Alternate Name][php-bug-47030]
(and OpenSSL also [lacking the low-level support][php-bug-55820] in PHP). If
these bugs are fixed, support for common name checking will be enabled by
default.

[Mozilla certificate authority list]: http://www.mozilla.org/projects/security/certs/
[php-bug-47030]: https://bugs.php.net/bug.php?id=47030
[php-bug-55820]:https://bugs.php.net/bug.php?id=55820