<?php

namespace Buzy\Tests\Cookie;

use Buzy\Cookie\CookieListener;
use Buzy\BrowserEvent;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CookieListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnRequest()
    {
        $request = Request::create('http://example.com/dir/test', 'GET', array(), array('coq' => 'coqval'));
        $response = Response::create();

        $listener = $this->getListener();
        $listener->onRequest(new BrowserEvent($request, $response));

        $expectedCookies = array(
            'foo' => 'fooval',
            'bar' => 'barval',
            'coq' => 'coqval',
        );

        $this->assertEquals($expectedCookies, $request->cookies->all(), 'Request get cookies from the jar');
    }

    public function testOnResponse()
    {
        $request = Request::create('http://example.com/dir/test');
        $response = Response::create();
        $response->headers->set('Set-Cookie', 'foo=foofoo');

        $listener = $this->getListener();
        $listener->onResponse(new BrowserEvent($request, $response));

        $this->assertEquals('foofoo', $listener->getJar()->get('foo', '/dir', 'example.com')->getValue());
    }

    private function getListener()
    {
        $jar = new CookieJar();
        $jar->set(new Cookie('foo', 'fooval', time()+10, '/dir', 'example.com'));
        $jar->set(new Cookie('bar', 'barval', time()+10, '/', '.example.com'));
        $jar->set(new Cookie('baz', 'bazval', time()-10, '/', 'example.com'));
        $jar->set(new Cookie('quz', 'quzval', time()+10, '/', 'other.com'));

        return new CookieListener($jar);
    }
}
