<?php

namespace DS\Queue\Consumer;

use DS\Queue\Job\Job;

/**
 * A Consumer is responsible for executing a Task on a Job
 *
 * If the queue processsing here is based on a visitor pattern, then the
 * Queue objects are the algorithm and the Consumer is the visitor. It knows
 * what jobs it can execute and how to map a job to an actual Task object
 * (although in theory, it could do the dispatching itself).
 *
 * The nice thing about the Consumer is that it lets the different queue
 * backends figure out how to map the specialized consumer to its pool of
 * jobs, while also letting it handle the execution. The Consumer itself
 * is also handy because it's a single interface for execution, meaning you
 * could easily extend or decorate it. For example, if you wanted each task to
 * run in a separate process so you didn't have to care about memory leaks,
 * then you could write a decorator for the Consumer to easily do that with
 * any other set of queue, plugins, etc.
 *
 * You can also do framework integration here. A lazy loading consumer could be
 * written for each framework's custom DI layer.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
interface Consumer
{
    /**
     * Execute the given job by passing it to a Task object
     *
     * @throws \DS\Queue\Exception\UnknownTaskException
     * @throws \DS\Queue\Exception\InvalidTaskException
     * @param Job $job
     * @return void
     */
    public function execute(Job $job);

    /**
     * Return all tasks this consumer is capable of executing
     *
     * If you have multiple workers running on multiple servers and you want to
     * specialize them, you can tweak the output of this function to control
     * which type of jobs the queue will give you.
     *
     * @return string[]
     */
    public function getAvailableTaskIds();
} 