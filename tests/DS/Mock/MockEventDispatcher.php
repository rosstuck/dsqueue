<?php

namespace DS\Mock;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class MockEventDispatcher extends EventDispatcher
{
    private $collectedEvents = [];

    public function dispatch($eventName, Event $event = null)
    {
        $this->collectedEvents[] = $event;
        return parent::dispatch($eventName, $event);
    }

    /**
     * @return Event[]
     */
    public function dequeueEvents()
    {
        $events = $this->collectedEvents;
        $this->collectedEvents = [];
        return $events;
    }
}