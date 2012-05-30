<?php

namespace Buzy\Tests\Client;

use Buzy\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Buzy\Client\Curl;

class CurlTest extends TestCase
{
    public function testSend()
    {
        $client = new Curl();

        $request = Request::create('http://www.google.com/robots.txt', 'GET');
        $response = new Response();
        $response->setStatusCode(100);

        $client->send($request, $response);

        $this->assertNotEquals('', $response->getContent());
        $this->assertNotEquals(0, count($response->headers));
        $this->assertEquals(200, $response->getStatusCode());
    }
}