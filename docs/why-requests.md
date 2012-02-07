Why Requests Instead of X?
==========================
This is a quick look at why you should use Requests instead of another
solution. Keep in mind though that these are my point of view, and they may not
be issues for you.

As always with software, you should choose what you think is best.


cURL
----
1. **Not every host has cURL installed**

   cURL is far from being ubiquitous, so you can't rely on it always being
   available when distributing software.

2. **cURL's interface sucks**

   cURL's interface was designed for PHP 4, and hence uses resources with
   horrible functions such as `curl_setopt()`. Combined with that, it uses 229
   global constants, polluting the global namespace horribly.

   Requests, on the other hand, exposes only a handleful of classes to the
   global namespace.


fsockopen
---------
1. **Very low-level**

   fsockopen is used for working with sockets directly, so it only knows about
   the transport layer (TCP in our case), not anything higher (i.e. HTTP on the
   application layer). To be able to use fsockopen as a HTTP client, you need
   to write all the HTTP code yourself, and once you're done, you'll end up
   with something that is almost exactly like Requests.


PEAR HTTP_Request2
------------------
1. **Requires PEAR**

   PEAR is a great distribution system (with a less than wonderful
   implementation), however it is not ubiquitous, as many hosts disable it to
   save on space that most people aren't going to use anyway.

   PEAR is also a pain for users. Users want to be able to download a zip of
   your project without needing to install anything else from PEAR.

   (If you really want though, Requests is available via PEAR. Check the README
   to see how to grab it.)

2. **Depends on other PEAR utilities**

   HTTP_Request2 requires Net_URL2 in order to function, locking you in to
   using PEAR for your project.

   Requests is entirely self-contained, and includes all the libraries it needs
   (for example, Requests_IRI is based on ComplexPie_IRI by Geoffrey Sneddon).


PECL HttpRequest
----------------
1. **Requires a PECL extension**

   Similar to PEAR, users aren't big fans of installing extra libraries. Unlike
   PEAR though, PECL extensions require compiling, which end users will be
   unfamiliar with. In addition, on systems where users do not have full
   control over PHP, they will be able to install custom extensions.


Zend Framework's Zend_Http_Client
---------------------------------
1. **Requires other parts of the Zend Framework**

   Similar to HTTP_Request2, Zend_Http_Client is not fully self-contained and
   requires other components from the framework.
