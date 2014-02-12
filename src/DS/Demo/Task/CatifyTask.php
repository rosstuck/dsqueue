<?php

namespace DS\Demo\Task;

use DS\Queue\Job\Job;
use DS\Queue\Task\Task;

/**
 * Adds cat emoticons. I mean, why not?
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class CatifyTask implements Task
{
    /**
     * @param Job $job
     * @return void
     */
    public function execute(Job $job)
    {
        return $job->setContent('=^.^= ' . $job->getContent() . ' =^.^=');
    }
}