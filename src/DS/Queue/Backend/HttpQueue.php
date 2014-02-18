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
 * @author Ross Tuck <me@rosstuck.com>
 */
class HttpQueue implements Queue
{

    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function queue(Job $job)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function processNextJob(Task $task)
    {
    }
}