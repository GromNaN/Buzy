<?php

namespace Buzy\Listener;

use Buzy\BrowserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
abstract class AbstractListener implements EventSubscriberInterface
{
    /**
     * @param \Buzy\BrowserEvent $event
     */
    abstract public function onRequest(BrowserEvent $event);

    /**
     * @param \Buzy\BrowserEvent $event
     */
    abstract public function onResponse(BrowserEvent $event);

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
