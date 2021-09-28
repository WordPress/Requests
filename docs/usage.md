Making a request
================

Ready to go? Make sure you have Requests [installed][download] and bootstrapped either the
Composer `autoload.php` file, the Requests autoload function or your autoloader, before attempting any of the
steps in this guide.

[download]: {{ '/download/' | prepend: site.baseurl }}


Make a GET Request
------------------
One of the most basic things you can do with HTTP is make a GET request.

Let's grab GitHub's public events:

```php
$response = WpOrg\Requests\Requests::get('https://api.github.com/events');
```

`$response` is now a **WpOrg\Requests\Response** object. Response objects are what
you'll be working with whenever you want to get data back from your request.


Using the Response Object
-------------------------
Now that we have the response from GitHub, let's get the body of the response.

```php
var_dump($response->body);
// string(42865) "[{"id":"15624773365","type":"PushEvent","actor":{...
```


Custom Headers
--------------
If you want to add custom headers to the request, simply pass them in as an
associative array as the second parameter:

```php
$response = WpOrg\Requests\Requests::get('https://api.github.com/events', array('X-Requests' => 'Is Awesome!'));
```


Make a POST Request
-------------------
Making a POST request is very similar to making a GET:

```php
$response = WpOrg\Requests\Requests::post('https://httpbin.org/post');
```

You'll probably also want to pass in some data. You can pass in either a
string, an array or an object (Requests uses [`http_build_query`][build_query]
internally) as the third parameter (after the URL and headers):

[build_query]: https://www.php.net/http_build_query

```php
$data = array('key1' => 'value1', 'key2' => 'value2');
$response = WpOrg\Requests\Requests::post('https://httpbin.org/post', array(), $data);
var_dump($response->body);
```

This gives the output:

    string(503) "{
      "origin": "124.191.162.147",
      "files": {},
      "form": {
        "key2": "value2",
        "key1": "value1"
      },
      "headers": {
        "Content-Length": "23",
        "Accept-Encoding": "deflate;q=1.0, compress;q=0.5, gzip;q=0.5",
        "X-Forwarded-Port": "80",
        "Connection": "keep-alive",
        "User-Agent": "php-requests/1.6-dev",
        "Host": "httpbin.org",
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
      },
      "url": "https://httpbin.org/post",
      "args": {},
      "data": ""
    }"

To send raw data, simply pass in a string instead. You'll probably also want to
set the Content-Type header to ensure the remote server knows what you're
sending it:

```php
$url = 'https://api.github.com/some/endpoint';
$headers = array('Content-Type' => 'application/json');
$data = array('some' => 'data');
$response = WpOrg\Requests\Requests::post($url, $headers, json_encode($data));
```

Note that if you don't manually specify a Content-Type header, Requests has
undefined behaviour for the header. It may be set to various values depending
on the internal execution path, so it's recommended to set this explicitly if
you need to.


Status Codes
------------
The Response object also gives you access to the status code:

```php
var_dump($response->status_code);
// int(200)
```

You can also easily check if this status code is a success code, or if it's an
error:

```php
var_dump($response->success);
// bool(true)
```


Response Headers
----------------
We can also grab headers pretty easily:

```php
var_dump($response->headers['Date']);
// string(29) "Thu, 09 Feb 2021 15:22:06 GMT"
```

Note that this is case-insensitive, so the following are all equivalent:

```php
$response->headers['Date']
$response->headers['date']
$response->headers['DATE']
$response->headers['dAtE']
```

If a header isn't set, this will give `null`. You can also check with
`isset($response->headers['date'])`

***

Previous: [Why should I use Requests instead of X?](why-requests.md)

Next: [Advanced usage](usage-advanced.md)
