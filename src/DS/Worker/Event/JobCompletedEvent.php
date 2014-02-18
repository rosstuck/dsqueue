<?php

namespace DS\Worker\Event;

use DS\Queue\Job\Job;
use DS\Queue\Queue;
use Symfony\Component\EventDispatcher\Event;

/**
 * Indicates that the queue successfully completed a job
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class JobCompletedEvent extends Event
{
    const NAME = 'worker.job_completed';

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @param Queue $queue The queue the job came from
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Returns the queue that the completed job originated from
     *
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }
}