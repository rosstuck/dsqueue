<?php

namespace DS\Mock;

use DS\Queue\Job\Job;

/**
 *  
 * @author Ross Tuck <me@rosstuck.com>
 */
class MockJob implements Job
{
    /**
     * @var string
     */
    protected $taskId;

    /**
     * @param string $taskId
     */
    public function __construct($taskId)
    {
        $this->taskId = $taskId;
    }

    public function getTaskId()
    {
        return $this->taskId;
    }
}