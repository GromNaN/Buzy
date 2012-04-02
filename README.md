Buzy is a an HTTP client for PHP built on top of Symfony2 components
====================================================================


Requirements
------------

* PHP 5.3 +
* Symfony HttpFoundation
* Symfony Event Dispatcher
* Curl Extension

Usage
-----

```php

$browser = new Buzy\Browser();
$response = $browser->get('http://www.google.com');

echo $browser->getLastRequest()."\n";
echo $response;
```

You can also use the low-level HTTP classes directly.

```php

$request = new Symfony\Component\HttpFoundation\Request::create('http://google.com', 'GET');
$response = new Symfony\Component\HttpFoundation\Response();

$client = new Buzz\Client\FileGetContents();
$client->send($request, $response);

echo $request;
echo $response;
```

Simple reverse proxy
--------------------

With this 4 lines of code, you can re-send a request and transfert response.

```php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Suzy\Browser;

$request = Request::createFromGlobals();
$request->server->set('HTTP_HOST', 'internal-server');

$browser = new Browser();

$response = $browser->send($request);

$response->send();

// The response is sent back to the client
```

Potential usages
----------------

* Resolve external ESI into a Symfony application without any cache server like Varnish
* 