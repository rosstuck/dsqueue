<?php

namespace DS\Queue\Backend;

use DS\Queue\Exception\InvalidArgumentException;
use DS\Queue\Job\StandardJob;
use DS\Queue\Task\Task;
use Redis;
use DS\Queue\Consumer\Consumer;
use DS\Queue\Job\Job;
use DS\Queue\Queue;

/**
 * Simple redis backed queue, based on a list.
 *
 * TODO: Respect Consumer specializations (subkey per task?)
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class RedisQueue implements Queue
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var string
     */
    protected $queueKey;

    public function __construct(Redis $redis, $queueKey)
    {
        $this->redis = $redis;

        // Rough check
        if (empty($queueKey) || !is_string($queueKey)) {
            throw new InvalidArgumentException('Invalid queue key, must be a valid redis key name');
        }
        $this->queueKey = $queueKey;
    }

    /**
     * {@inheritdoc}
     */
    public function queue(Job $job)
    {
        $this->redis->lpush($this->queueKey, $job);
    }

    /**
     * {@inheritdoc}
     */
    public function processNextJob(Task $task)
    {
        $job = $this->redis->rpop($this->queueKey);
        if (!$job) {
            return Queue::RESULT_NO_JOB;
        }

        $task->execute(new StandardJob($job));
        return Queue::RESULT_JOB_COMPLETE;
    }
}