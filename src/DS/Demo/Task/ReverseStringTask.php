<?php

namespace DS\Demo\Task;

use DS\Queue\Job\Job;
use DS\Queue\Task\Task;

/**
 * Reverse the string. Kind of the hello world of queueing.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class ReverseStringTask implements Task
{
    /**
     * @param Job $job
     * @return void
     */
    public function execute(Job $job)
    {
        $job->setContent(strrev($job->getContent()));
    }
}