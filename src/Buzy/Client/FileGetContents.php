<?php

namespace Buzy\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileGetContents implements ClientInterface
{
    /**
     * @see ClientInterface
     *
     * @throws RuntimeException If file_get_contents() fires an error
     */
    public function send(Request $request, Response $response)
    {
        $context = stream_context_create($x = $this->getStreamContextArray($request));

        $uri = $request->getUri();
        $level = error_reporting(0);
        $content = file_get_contents($uri, 0, $context);
        error_reporting($level);
        if (false === $content) {
            $error = error_get_last();
            throw new \RuntimeException($error['message']);
        }

        $this->parseHeader((array) $http_response_header, $response);
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
/*
                // values from the current client
                'ignore_errors'    => $this->getIgnoreErrors(),
                'max_redirects'    => $this->getMaxRedirects(),
                'timeout'          => $this->getTimeout(),
*/
            ),
/*
            'ssl' => array(
                'verify_peer'      => $this->getVerifyPeer(),
            ),

*/
        );
    }

    protected function parseHeader(array $headers, Response $response)
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
                $this->response->setCharset(trim($matches[1]));
            }
        }
    }
}
