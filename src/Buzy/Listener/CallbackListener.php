<?php

namespace Buzy\Listener;

use Buzy\BrowserEvent;

/**
 * Execute callbacks.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class CallbackListener extends AbstractListener
{
    private $callbacks;
    private $params;

    /**
     * Constructor.
     *
     * @param array $callbacks Closures to execute. "before" and "after"
     * @param array $params    Optionnal parameter passed as 2nd parameter
     *                         to the closure.
     */
    public function __construct(array $callbacks, $params = null)
    {
        $this->callbacks = $callbacks;
        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public function onRequest(BrowserEvent $event)
    {
        if (isset($this->callbacks['request'])) {
            call_user_func($this->callbacks['request'], $event, $this->params);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onResponse(BrowserEvent $event)
    {
        if (isset($this->callbacks['response'])) {
            call_user_func($this->callbacks['response'], $event, $this->params);
        }
    }
}

