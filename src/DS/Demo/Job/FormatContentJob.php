<?php

namespace DS\Demo\Job;

use DS\Queue\Job\SequentialJobWithPipeline;

/**
 * A simple test job.
 *
 * Here, we just mutate the state of the job to pass along our result. This is
 * fine for simple chaining of tasks. However, in practice, your Tasks would
 * probably publish this data to some other location.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class FormatContentJob extends SequentialJobWithPipeline
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @param string $content The string to change
     * @param string[] $taskPipeline The list of tasks you'd like to do
     */
    public function __construct($content, $taskPipeline)
    {
        $this->content = $content;
        $this->setTaskPipeline($taskPipeline);
    }

    /**
     * The current content to mutate
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Update the content for the next Task
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}