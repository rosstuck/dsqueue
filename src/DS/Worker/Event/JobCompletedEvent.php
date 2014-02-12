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
     * @var Job
     */
    protected $job;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @param Job $job The completed job
     * @param Queue $queue The queue the job came from
     */
    public function __construct(Job $job, Queue $queue)
    {
        $this->job = $job;
        $this->queue = $queue;
    }

    /**
     * Returns the job that was just completed in the worker
     *
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
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