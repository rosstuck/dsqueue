<?php

namespace DS\Queue\Backend;

use DS\Queue\Consumer\Consumer;
use DS\Queue\Job\Job;
use DS\Queue\Queue;

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
    public function processNextJob(Consumer $consumer)
    {
        $job = $this->getNextJob($consumer->getAvailableTaskIds());
        if (!$job) {
            return Queue::RESULT_NO_JOB;
        }

        $consumer->execute($job);
        return $job;
    }

    /**
     * Fetch a job from the queue if it has one of the given task ids
     *
     * @param array $taskIds
     * @return Job|null
     */
    protected function getNextJob($taskIds)
    {
        foreach ($this->pendingJobs as $key => $job) {
            if (in_array($job->getTaskId(), $taskIds)) {
                unset($this->pendingJobs[$key]);
                return $job;
            }
        }
    }
}