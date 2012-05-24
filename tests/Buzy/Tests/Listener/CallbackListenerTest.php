<?php

namespace Buzy\Tests\Listener;

use Buzy\Listener\CallbackListener;
use Buzy\BrowserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CallbackListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testCallbacks()
    {
        $request = new Request();
        $response = new Response();

        $event = new BrowserEvent($request, $response);

        $this->requestExecuted = false;
        $this->responseExecuted = false;

        $listener = new CallbackListener(array(
            'request' => function ($eventParameter, $test) use ($event) {
                $test->assertSame($event, $eventParameter);
                $test->requestExecuted = true;
            },
            'response' => function ($eventParameter, $test) use ($event) {
                $test->assertSame($event, $eventParameter);
                $test->responseExecuted = true;
            },
        ), $this);

        $listener->onRequest($event);
        $listener->onResponse($event);

        $this->assertTrue($this->requestExecuted, 'The request callback has been executed');
        $this->assertTrue($this->responseExecuted, 'The response callback has been executed');
    }
}
