<?php

namespace DS\Queue\Backend;

use DS\Queue\Consumer\Consumer;
use DS\Queue\Job\Job;
use DS\Queue\Job\StandardJob;
use DS\Queue\Queue;
use DS\Queue\Task\Task;

/**
 * A simple array backed queue. Mostly useful for testing.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class InMemoryQueue implements Queue
{
    /**
     * @var Job[]
     */
    protected $pendingJobs = array();

    /**
     * {@inheritdoc}
     */
    public function queue(Job $job)
    {
        $this->pendingJobs[] = $job;
    }

    /**
     * {@inheritdoc}
     */
    public function processNextJob(Task $task)
    {
        $jobPayload = array_pop($this->pendingJobs);
        if (!$jobPayload) {
            return Queue::RESULT_NO_JOB;
        }

        $task->execute($jobPayload);
        return Queue::RESULT_JOB_COMPLETE;
    }
}