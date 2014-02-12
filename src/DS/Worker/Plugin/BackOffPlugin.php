<?php

namespace DS\Worker\Plugin;

use DS\Worker\Event\NoPendingJobEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Forces the worker to wait X number of seconds when there are no pending jobs
 *
 * This plugin basically keeps your CPU from spiking to 100%, so it's a good
 * idea to leave it on. This is implemented as a plugin so you could implement
 * your own back off strategies, such as decay over time, etc.
 *
 * This one checks the queue immediately if it just finished a job because
 * there might be more to do in the backlog. If there were no jobs in the last
 * pass, then it sleeps for one second before polling the queue again
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class BackOffPlugin implements EventSubscriberInterface
{
    /**
     * @var int
     */
    protected $waitTime;

    /**
     * @param int $waitTime Seconds to wait, when there are no jobs
     */
    public function __construct($waitTime)
    {
        $this->waitTime = $waitTime;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [NoPendingJobEvent::NAME => 'wait'];
    }

    /**
     * Wait for the specified amount of time
     */
    public function wait()
    {
        sleep($this->waitTime);
    }
}