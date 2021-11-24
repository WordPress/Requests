Custom Authentication
=====================

Custom authentication handlers are designed to be straight-forward to write.
In order to write a handler, you'll need to implement the `WpOrg\Requests\Auth`
interface.

An instance of this handler can then be passed to Requests via the `auth`
option, just like for normal authentication.

Let's say we have a HTTP endpoint that checks for the `Hotdog` header and
authenticates the call if said header is set to `Yummy`. (I don't know of any
services that do this; perhaps this is a market waiting to be tapped?)

```php
class MySoftware_Auth_Hotdog implements WpOrg\Requests\Auth {
    protected $password;

    public function __construct($password) {
        $this->password = $password;
    }

    public function register(WpOrg\Requests\Hooks &$hooks) {
        $hooks->register('requests.before_request', array($this, 'before_request'));
    }

    public function before_request(&$url, &$headers, &$data, &$type, &$options) {
        $headers['Hotdog'] = $this->password;
    }
}
```

We then use this in our request calls like this:

```php
$options = array(
    'auth' => new MySoftware_Auth_Hotdog('yummy')
);
$response = WpOrg\Requests\Requests::get('http://hotdogbin.org/admin', array(), $options);
```

For more information on how to register and use hooks, see the [hooking
system documentation][hooks].

[hooks]: hooks.md

***

Previous: [Authenticating your request](authentication.md)

Next: [Requests through proxy](proxy.md)
