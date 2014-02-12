<?php

namespace DS\Mock;

use DS\Queue\Job\SequentialJobWithPipeline;

/**
 *  
 * @author Ross Tuck <me@rosstuck.com>
 */
class MockSequentialJob extends SequentialJobWithPipeline
{
    /**
     * @param array|\Traversable $tasks
     */
    public function __construct($tasks)
    {
        $this->setTaskPipeline($tasks);
    }
}