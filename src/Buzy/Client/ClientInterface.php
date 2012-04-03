<?php

namespace Buzy\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Common interface for HTTP clients.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
interface ClientInterface
{
    /**
     * Populates the supplied response with the response for the supplied request.
     *
     * @param Request  $request  A request object
     *
     * @param Response $response A response object
     *
     * @throws \Buzy\Client\HttpException
     */
    function send(Request $request, Response $response);
}
