<?php

namespace DS\Queue\Task;

use DS\Queue\Job\Job;

/**
 * Accepts a Job and performs some operation on it.
 *
 * These are the actual operations you'd like done in the queue, for example
 * some heavy data processing or sending out an email.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
interface Task
{
    /**
     * Perform some operation using the Job
     *
     * @param Job $job
     * @return void
     */
    public function execute(Job $job);
}