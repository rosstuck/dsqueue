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
    protected $payload;

    /**
     * @param string $taskId
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}