<?php

namespace Buzy\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ClientInterface
{
    /**
     * Populates the supplied response with the response for the supplied request.
     *
     * @param Request  $request  A request object
     * @param Response $response A response object
     */
    function send(Request $request, Response $response);
}
