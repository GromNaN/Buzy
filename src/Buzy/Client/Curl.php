<?php

namespace Buzy\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP client using cURL extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class Curl implements ClientInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * @param array $options Client options
     */
    public function __construct(array $options = array())
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
     */
    public function send(Request $request, Response $response)
    {
        $curl = curl_init();

        curl_setopt_array($curl, $this->getCurlOptions($request));

        $data = curl_exec($curl);

        if (false === $data) {
            throw new HttpException(curl_error($curl), curl_errno($curl));
        }

        $this->parseResponse($response, $data);

        curl_close($curl);
    }

    protected function getCurlOptions(Request $request)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => true,
            CURLOPT_CUSTOMREQUEST   => $request->getMethod(),
            CURLOPT_URL             => $request->getUri(),
            CURLOPT_HTTPHEADER      => explode("\r\n", $request->headers),
            CURLOPT_HTTPGET         => false,
            CURLOPT_NOBODY          => false,
            CURLOPT_POSTFIELDS      => null,
            CURLOPT_TIMEOUT         => $this->options['timeout'],
            CURLOPT_FOLLOWLOCATION  => 0 < $this->options['max_redirects'],
            CURLOPT_MAXREDIRS       => $this->options['max_redirects'],
            CURLOPT_FAILONERROR     => !$this->options['ignore_errors'],
            CURLOPT_SSL_VERIFYPEER  => $this->options['verify_peer'],
        );

        switch ($request->getMethod()) {
            case 'HEAD':
                $options[CURLOPT_NOBODY] = true;
                break;

            case 'GET':
                $options[CURLOPT_HTTPGET] = true;
                break;

            default:
                $fields = array();
                $multipart = false;
                foreach ($request->files->all() as $key => $value) {
                    $multipart = true;
                    $fields[$key] = '@' . $value->getName() . ';type=' . $value->getMimeType();
                }
                foreach ($request->request->all() as $key => $value) {
                    $fields[$key] = $value;
                }

                if (!$multipart) {
                    $fields = http_build_query($fields);
                } else {
                    // remove the content-type header
                    $options[CURLOPT_HTTPHEADER] = array_filter($options[CURLOPT_HTTPHEADER], function($header)
                    {
                        return 0 !== stripos($header, 'Content-Type: ');
                    });
                }

                $options[CURLOPT_POSTFIELDS] = $fields;
        }

        return $options;
    }

    protected function parseResponse(Response $response, $data)
    {
        $lines = preg_split('/(\\r?\\n)/', $data, -1, PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0, $count = count($lines); $i < $count; $i += 2) {
            if (empty($lines[$i])) {
                $response->setContent(implode('', array_slice($lines, $i + 2)));
                break;
            }

            $header = $lines[$i];
            if (preg_match('#HTTP/([\.0-9]{3}) ([0-9]{3}) (.+)#', $header, $matches)) {
                $response
                    ->setProtocolVersion($matches[1])
                    ->setStatusCode($matches[2], trim($matches[3]))
                ;
            } elseif (preg_match('#([\w-]+): (.+)#', $header, $matches)) {
                $response->headers->set($matches[1], $matches[2]);
            } else {
                throw new HttpException(sprintf('Invalid HTTP response header: %s', $header));
            }
        }
    }
}
