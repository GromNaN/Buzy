<?php

namespace Buzy\Listener;

use Buzy\BrowserEvent;
use Symfony\Component\BrowserKit\CookieJar;

/**
 * Use the Symfony BrowserKit CookieJar to store HTTP cookies.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class CookieListener extends AbstractListener
{
    /**
     * @var Jar
     */
    private $jar;

    /**
     * Constructor.
     *
     * @param CookieJar $jar
     */
    public function __construct(CookieJar $jar = null)
    {
        $this->jar = $jar ?: new CookieJar();
    }

    /**
     * Get the Cookie Jar
     *
     * @return CookieJar
     */
    public function getJar()
    {
        return $this->jar;
    }

    /**
     * Add cookies to the Request with the related cookies of the Cookie Jar.
     *
     * @param BrowserEvent $event
     */
    public function onRequest(BrowserEvent $event)
    {
        $request = $event->getRequest();

        $request->cookies->replace(array_replace(
            $this->jar->allValues($request->getUri()),
            $request->cookies->all()
        ));
    }

    /**
     * Update the Cookie Jar to follow the response header instruction (add, update, delete)
     *
     * @param BrowserEvent $event
     */
    public function onResponse(BrowserEvent $event)
    {
        $cookies = $event->getResponse()->headers->get('Set-Cookie');
        $uri = $event->getRequest()->getUri();

        $this->jar->updateFromSetCookie(array($cookies), $uri);
    }
}