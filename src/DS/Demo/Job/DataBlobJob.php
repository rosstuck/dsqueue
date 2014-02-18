<?php

namespace DS\Demo\Job;

use DS\Queue\Queue;

/**
 * @author Ross Tuck <me@rosstuck.com>
 */
class DataBlobJob
{
    /**
     * @var string
     */
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * The current content to mutate
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}