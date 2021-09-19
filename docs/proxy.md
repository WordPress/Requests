---
layout: documentation
title: Proxy Support
---

Proxy Support
=============

Making requests through HTTP proxies is fully supported.

To make requests through an open proxy, specify the following options:

```php
$options = array(
    'proxy' => '127.0.0.1:3128'
);
WpOrg\Requests\Requests::get('https://httpbin.org/ip', array(), $options);
```

If your proxy needs you to authenticate, the option will become an array like
in the following example:

```php
$options = array(
    'proxy' => array( '127.0.0.1:3128', 'my_username', 'my_password' )
);
WpOrg\Requests\Requests::get('https://httpbin.org/ip', array(), $options);
```

***

Previous: [Custom authentification](authentication-custom.html)

Next: [Hooking system](hooks.html)
