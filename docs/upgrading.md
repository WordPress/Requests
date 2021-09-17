Upgrading to Requests 2.0
=========================

Autoloading the Requests library
--------------------------------

In Requests 1.x, both a custom autoloader native to Requests, as well as autoloading
via the Composer `vendor/autoload.php` file was supported.

The same applies to Requests 2.x, though the way to initiate the Requests native
custom autoloader has changed.

If you were autoloading the Requests library via the Composer autoload file,
no changes are needed.

```php
// OLD: Using the custom autoloader in Requests 1.x.
require_once 'path/to/Requests/library/Requests.php';
Requests::register_autoloader();

// NEW: Using the custom autoloader in Requests 2.x.
require_once 'path/to/Requests/src/Autoload.php';
WpOrg\Requests\Autoload::register();
```

For backward compatibility reasons, the Requests 1.x manner to call the autoloader
will still be supported for the time being, but it is recommended to update
your code to use the new autoloader.


New namespaced names
--------------------

As of Requests 2.0.0, all code in the Requests library is namespaced and
class/interface names have been normalized to CamelCaps.

We recommend for all users to upgrade their code to use the new namespaced
class names.

| Type      | Request 1.x name                             | Requests >= 2.0 name                                                    |
|-----------|----------------------------------------------|-------------------------------------------------------------------------|
| class     | `Requests`                                   | `WpOrg\Requests\Requests` <strong><sup>1</sup></strong>                 |
| interface | `Requests_Auth`                              | `WpOrg\Requests\Auth`                                                   |
| interface | `Requests_Hooker`                            | `WpOrg\Requests\Hooker`                                                 |
| interface | `Requests_Transport`                         | `WpOrg\Requests\Transport`                                              |
| interface | `Requests_Proxy`                             | `WpOrg\Requests\Proxy`                                                  |
| class     | `Requests_Cookie`                            | `WpOrg\Requests\Cookie`                                                 |
| class     | `Requests_Exception`                         | `WpOrg\Requests\Exception`                                              |
| class     | `Requests_Hooks`                             | `WpOrg\Requests\Hooks`                                                  |
| class     | `Requests_IDNAEncoder`                       | `WpOrg\Requests\IdnaEncoder`                                            |
| class     | `Requests_IPv6`                              | `WpOrg\Requests\Ipv6`                                                   |
| class     | `Requests_IRI`                               | `WpOrg\Requests\Iri`                                                    |
| class     | `Requests_Response`                          | `WpOrg\Requests\Response`                                               |
| class     | `Requests_Session`                           | `WpOrg\Requests\Session`                                                |
| class     | `Requests_SSL`                               | `WpOrg\Requests\Ssl`                                                    |
| class     | `Requests_Auth_Basic`                        | `WpOrg\Requests\Auth\Basic`                                             |
| class     | `Requests_Cookie_Jar`                        | `WpOrg\Requests\Cookie\Jar`                                             |
| class     | `Requests_Proxy_HTTP`                        | `WpOrg\Requests\Proxy\Http`                                             |
| class     | `Requests_Response_Headers`                  | `WpOrg\Requests\Response\Headers`                                       |
| class     | `Requests_Transport_cURL`                    | `WpOrg\Requests\Transport\Curl`                                         |
| class     | `Requests_Transport_fsockopen`               | `WpOrg\Requests\Transport\Fsockopen`                                    |
| class     | `Requests_Utility_CaseInsensitiveDictionary` | `WpOrg\Requests\Utility\CaseInsensitiveDictionary`                      |
| class     | `Requests_Utility_FilteredIterator`          | `WpOrg\Requests\Utility\FilteredIterator`                               |
| class     | `Requests_Exception_HTTP`                    | `WpOrg\Requests\Exception\Http`                                         |
| class     | `Requests_Exception_Transport`               | `WpOrg\Requests\Exception\Transport`                                    |
| class     | `Requests_Exception_Transport_cURL`          | `WpOrg\Requests\Exception\Transport\Curl`                               |
| class     | `Requests_Exception_HTTP_###`                | `WpOrg\Requests\Exception\Http\Status###` <strong><sup>2</sup></strong> |
| class     | `Requests_Exception_HTTP_Unknown`            | `WpOrg\Requests\Exception\Http\StatusUnknown`                           |


**Notes**:

<sup>1</sup> The original `Requests` class has been split into two classes:
* `WpOrg\Requests\Requests` contains the actual functionality;
* `WpOrg\Requests\Autoload` contains the custom autoloader.
* The original non-namespaced `Requests` class remains for backward-compatibility
    reasons and extends `WpOrg\Requests\Requests` and uses `WpOrg\Requests\Autoload`
    in the old autoload functions, `Requests::autoloader()` and
    `Requests::register_autoloader()`.

<sup>2</sup> The `###` in the HTTP Exception classes represents a three character,
numeric HTTP status code.
Exception classes are available for HTTP status 304 - 306, 400 - 418,
428, 429, 431, 500 - 505 and 511.


Backward compatibility layer
----------------------------

If for some reason, you can not yet upgrade or have to support both Requests 1.x
as well as Requests >= 2.0, a backward compatibility layer is provided.
This backward compatibility layer will work with both the Requests native
custom autoloader, as well as when using the Composer `vendor/autoload.php` file.

Using the old PSR-0 names will still work, but a deprecation notice will be
thrown the first time during a page load when a PSR-0 class name is referenced.

For the lifetime of Requests 2.x, the deprecation notices can be disabled by
defining a global `REQUESTS_SILENCE_PSR0_DEPRECATIONS` constant and
setting the value of this constant to `true`.
The constant needs to be defined before the first PSR-0 class name is requested.

As of Requests 3.0, support for the constant will be removed and
the deprecation notice will always be thrown.

As of Requests 4.0, the backward compatibility layer will be removed.

***

Previous: [Hooks](hooks.md)
