Buzy is an HTTP client for PHP built on top of Symfony2 components
====================================================================

This is a work in progress project. The goal is to build an efficient and robust
library on top of Symfony2 HttpFoundation (Request and Response).

Todo:

* HTTP cache listener to skip not necessary requests.
* CURL client
* Proxy support
* Cookie Jar listener
* History listener
* Logger listener

Requirements
------------

* PHP 5.3 +
* Symfony HttpFoundation
* Symfony EventDispatcher
* Symfony BrowserKit (for CookieJar)
* Curl Extension (not yet)

Usage
-----

```php

$browser = new Buzy\Browser();
$response = $browser->get('http://www.google.com');

echo $response;
```

You can also use the low-level HTTP classes directly.

```php

$request = new Symfony\Component\HttpFoundation\Request::create('http://google.com', 'GET');
$response = new Symfony\Component\HttpFoundation\Response();

$client = new Buzy\Client\FileGetContents();
$client->send($request, $response);

echo $request;
echo $response;
```

Simple reverse proxy
--------------------

With this 5 lines of code, you can re-send a request and transfert the response.

```php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Buzy\Browser;

$request = Request::createFromGlobals();
$request->server->set('HTTP_HOST', 'internal-server');

$browser = new Browser();

$response = $browser->send($request);

$response->send();

// The response is sent back to the client
```

Licence
-------

Original code base is extracted from Buzz library written by Kris Wallsmith.

This library is shared under MIT licence. See LICENCE file.
