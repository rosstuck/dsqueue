<?php

namespace DS\Queue\Consumer;

use DS\Queue\Job\Job;
use DS\Queue\Task\Task;
use DS\Queue\Exception\InvalidTaskException;
use DS\Queue\Exception\UnknownTaskException;

/**
 * Implements a lazy loading consumer based on Pimple
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class LazyConsumer implements Consumer
{
    /**
     * @var \Pimple
     */
    protected $container;

    /**
     * @var array
     */
    protected $tasks = array();

    /**
     * @param \Pimple $container
     */
    public function __construct(\Pimple $container)
    {
        $this->container = $container;
    }

    /**
     * Register a service in the container as being a Task
     *
     * @param string $taskId
     * @param string $serviceId
     */
    public function registerTask($taskId, $serviceId)
    {
        $this->tasks[$taskId] = $serviceId;
    }

    public function execute(Job $job)
    {
        $this->getTask($job->getTaskId())->execute($job);
    }

    public function getAvailableTaskIds()
    {
        // Yes, yes, could be cached.
        return array_keys($this->tasks);
    }

    /**
     * @param string $taskId
     * @throws \DS\Queue\Exception\UnknownTaskException
     * @throws \DS\Queue\Exception\InvalidTaskException
     * @return Task
     */
    protected function getTask($taskId)
    {
        if (!isset($this->tasks[$taskId])) {
            throw new UnknownTaskException("Could not find task by id '{$taskId}'");
        }

        $task = $this->container[$this->tasks[$taskId]];
        if (!$task instanceof Task) {
            throw new InvalidTaskException("Service '{$this->tasks[$taskId]}' does not implement Task");
        }

        return $task;
    }
}