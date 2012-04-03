<?php

namespace Buzy\Cache;

use Buzy\BrowserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Cache\Cache;

/**
 * HTTP Cache listener provide standard cache features.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class CacheListener implements EventSubscriberInterface
{
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * Constructor.
     *
     * @param \Doctrine\Common\Cache\Cache $cache The cache adapter
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Try to find a cached response before the resquest is sent
     *
     * @todo Implement HTTP cache rules
     *
     * @param \Buzy\BrowserEvent $event
     */
    public function onRequest(BrowserEvent $event)
    {
        $id = $this->generateRequestIdentifier($event->getRequest());

        if ($this->cache->contains($id)) {
            $cachedResponse = unserialize($this->cache->fetch($id));

            $event->getResponse()->headers = clone $cachedResponse->headers;

            $event->getResponse()
                ->setContent($cachedResponse->getContent())
                ->setContentType($cachedResponse->getContentType())
                ->setProtocolVersion($cachedResponse->getProtocolVersion())
                ->setStatusCode($cachedResponse->getStatusCode())
                ->setEtag($cachedResponse->getEtag())
                ->headers
                    ->set('Age', $cachedResponse->headers->get('Age') + time() - $cachedResponse->getDate()->format('U'))
            ;

            $event->stopPropagation();
        }
    }

    /**
     * Store the response if cachable.
     *
     * @param \Buzy\BrowserEvent $event
     */
    public function onResponse(BrowserEvent $event)
    {
        $response = $event->getResponse();

        if ($response->isCacheable()) {
            $id = $this->generateRequestIdentifier($event->getRequest());
            $ttl = max($response->getTtl() - $response->getAge(), 0);
            $this->cache->save($id, serialize($response));
        }
    }

    protected function generateRequestIdentifier(Request $request)
    {
        return sha1($request->headers->all());
    }

    /**
     * {@inheritDoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            BrowserEvent::REQUEST  => 'onRequest',
            BrowserEvent::RESPONSE => 'onResponse',
        );
    }
}
