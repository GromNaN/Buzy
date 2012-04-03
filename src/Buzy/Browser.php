<?php

namespace Buzy;

use Buzy\Client\ClientInterface;
use Buzy\Client\FileGetContents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Web browser providing convenient method for HTTP requests.
 * API is identical with Buzz by Kris Wallsmith
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class Browser
{
    private $client;
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param \Buzy\Client\ClientInterface $client HTTP Client adapter
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(ClientInterface $client = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->client = $client ?: new FileGetContents();
        $this->dispatcher = $dispatcher;
    }

    public function get($url, $headers = array())
    {
        return $this->call($url, 'GET', $headers);
    }

    public function post($url, $headers = array(), $content = '')
    {
        return $this->call($url, 'POST', $headers, $content);
    }

    public function head($url, $headers = array())
    {
        return $this->call($url, 'HEAD', $headers);
    }

    public function put($url, $headers = array(), $content = '')
    {
        return $this->call($url, 'PUT', $headers, $content);
    }

    public function delete($url, $headers = array(), $content = '')
    {
        return $this->call($url, 'DELETE', $headers, $content);
    }

    /**
     * Sends a request.
     *
     * @param string $url     The URL to call
     * @param string $method  The request method to use
     * @param array  $headers An array of request headers
     * @param string $content The request content
     *
     * @return Response The response object
     */
    public function call($url, $method, $headers = array(), $content = '')
    {
        $request = Request::create($url, $method, array(), array(), array(), array(), $content);

        foreach ($headers as $key => $value) {
            if (is_numeric($key)) {
                list($key, $value) = explode(':', $value, 2);
            }

            $request->headers->set($key, trim($value));
        }

        return $this->send($request);
    }

    /**
     * Sends a form request.
     *
     * @param string $url     The URL to submit to
     * @param array  $fields  An array of fields
     * @param string $method  The request method to use
     * @param array  $headers An array of request headers
     *
     * @return Response The response object
     */
    public function submit($url, array $fields, $method = 'POST', $headers = array())
    {
        $request = Request::create($url, $method, $fields, array(), array(), array(), null);

        foreach ($headers as $key => $value) {
            if (is_numeric($key)) {
                list($key, $value) = explode(':', $value, 2);
            }

            $request->headers->set($key, trim($value));
        }

        return $this->send($request);
    }

    /**
     * Sends a request.
     *
     * @param Request  $request  A request object
     * @param Response $response A response object
     *
     * @return Response A response object
     */
    public function send(Request $request, Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        if (null !== $this->dispatcher) {
            $event = new BrowserEvent($request, $response);
            $this->dispatcher->dispatch(BrowserEvent::REQUEST, $event);

            if ($event->isPropagationStopped()) {
                return $response;
            }
        }

        $this->client->send($request, $response);

        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(BrowserEvent::RESPONSE, $event);
        }

        return $response;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }
}
