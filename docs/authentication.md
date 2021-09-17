Authentication
==============
Many requests that you make will require authentication of some type. Requests
includes support out of the box for HTTP Basic authentication, with more
built-ins coming soon.

A Basic authenticated call can be made like this:

```php
$options = array(
    'auth' => new WpOrg\Requests\Auth\Basic(array('user', 'password'))
);
WpOrg\Requests\Requests::get('https://httpbin.org/basic-auth/user/password', array(), $options);
```

As Basic authentication is usually what you want when you specify a username
and password, you can also just pass in an array as a shorthand:

```php
$options = array(
    'auth' => array('user', 'password')
);
WpOrg\Requests\Requests::get('https://httpbin.org/basic-auth/user/password', array(), $options);
```

Note that `POST`/`PUT` requests take a `$data` parameter, so you need to pass that
before `$options`:

```php
WpOrg\Requests\Requests::post('https://httpbin.org/basic-auth/user/password', array(), null, $options);
```

***

Previous: [Advanced usage](usage-advanced.md)

Next: [Custom authentification](authentication-custom.md)
