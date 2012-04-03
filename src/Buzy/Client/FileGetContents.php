<?php

namespace Buzy\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP client on top of file_get_contents function.
 * Requires "allow_url_fopen = On" in php.ini
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class FileGetContents implements ClientInterface
{
    /**
     * Client options.
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->options = array_replace(array(
            'ignore_errors' => true,
            'max_redirects' => 10,
            'timeout'       => 30,
            'verify_peer'   => false,
        ), $options);
    }

    /**
     * {@inheritDoc}
     *
     * @see ClientInterface
     *
     * @throws \Buzy\Client\HttpException If file_get_contents() fires an error
     */
    public function send(Request $request, Response $response)
    {
        $uri = $request->getUri();
        $context = stream_context_create($this->getStreamContextArray($request));

        $level = error_reporting(0);
        $content = file_get_contents($uri, 0, $context);
        error_reporting($level);

        if (false === $content) {
            $error = error_get_last();
            throw new HttpException($error['message']);
        }

        $this->parseHeaders((array) $http_response_header, $response);
        $response->setContent($content);
    }

    /**
     * Converts a request into an array for stream_context_create().
     *
     * @param Message\Request $request A request object
     *
     * @return array An array for stream_context_create()
     */
    protected function getStreamContextArray(Request $request)
    {
        return array(
            'http' => array(
                // values from the request
                'method'           => $request->getMethod(),
                'header'           => strval($request->headers),
                'content'          => $request->getContent(),
                'protocol_version' => $request->server->get('SERVER_PROTOCOL'),

                // values from the current client
                'ignore_errors'    => $this->options['ignore_errors'],
                'max_redirects'    => $this->options['max_redirects'],
                'timeout'          => $this->options['timeout'],
            ),
            'ssl' => array(
                'verify_peer'      => $this->options['verify_peer'],
            ),
        );
    }

    protected function parseHeaders(array $headers, Response $response)
    {
        // @todo cookies
        foreach ($headers as $header) {
            if (preg_match('#HTTP/([\.0-9]{3}) ([0-9]{3}) (.+)#', $header, $matches)) {
                $response
                    ->setProtocolVersion($matches[1])
                    ->setStatusCode($matches[2], trim($matches[3]))
                ;
            } elseif (preg_match('#([\w-]+): (.+)#', $header, $matches)) {
                $response->headers->set($matches[1], $matches[2]);
            }
        }

        if ($response->headers->has('content-type')) {
            if (preg_match('#.*;charset=(.*)#', $response->headers->get('content-type'), $matches)) {
                $this->response->setCharset(strtoupper(trim($matches[1])));
            }
        }
    }
}
