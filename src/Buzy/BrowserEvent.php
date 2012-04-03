<?php

namespace Buzy;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Event for Symfony Event Dispatcher.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class BrowserEvent extends Event
{
    const REQUEST = 'buzy.request';
    const RESPONSE = 'buzy.response';

    private $request;
    private $response;

    /**
     * Constuctor.
     *
     * @param \Symfony\Component\HttpFoundation\Request  $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
