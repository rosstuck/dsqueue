<?php

namespace DS\Queue\Job;

/**
 *  
 * @author Ross Tuck <me@rosstuck.com>
 */
class StandardJob implements Job
{
    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
} 