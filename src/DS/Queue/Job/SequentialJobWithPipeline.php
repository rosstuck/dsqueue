<?php

namespace DS\Queue\Job;

/**
 * Base class to implement a simple version of a SequentialJob
 *
 * All you need to do is call setTaskPipeline() with an array of tasks before
 * queueing your job for the first time. This will progress through the array
 * of tasks until it's complete.
 *
 * This is a really simple strategy for navigating a piepline but it's a decent
 * example. You could also, for example, have a PipelineId in your job and then
 * write a plugin to lookup the next job in the piepline, based on the id.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
abstract class SequentialJobWithPipeline implements Job, SequentialJob
{
    /**
     * @var \Iterator
     */
    protected $taskIds;

    /**
     * @var int
     */
    protected $currentIndex = 0;

    /**
     * Set the pipeline of tasks you'd like to execute
     *
     * @param string[] $taskIds
     */
    public function setTaskPipeline($taskIds)
    {
        $this->taskIds = $this->normalizeArray($taskIds);
    }

    /**
     * Return the id of the task you'd like to be executed next
     *
     * @return string
     */
    public function getTaskId()
    {
        if (isset($this->taskIds[$this->currentIndex])) {
            return $this->taskIds[$this->currentIndex];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function advanceToNextTask()
    {
        $this->currentIndex++;
    }

    /**
     * {@inheritdoc}
     */
    public function isComplete()
    {
        return empty($this->taskIds) || $this->currentIndex === (count($this->taskIds) - 1);
    }

    /**
     * Convert an array to a flat, numerically indexed array.
     *
     * This class is paranoid about doing so because we want to serialize and
     * deserialize it in the queue but retain the current index. We can't count
     * on using a value, for example, because the same task might appear more
     * than once in a pipeline. We could track the original key but that might
     * be less serializable and we'd need to scroll through each time. This is
     * a bit easier than repeatedly looping over previous keys.
     *
     * @param array|Traversable $array
     * @return array
     * @throws \DS\Queue\Exception\InvalidArgumentException
     */
    protected function normalizeArray($array)
    {
        // Array_values resets
        if (is_array($array)) {
            return array_values($array);
        }

        if (is_object($array) && $array instanceof \Traversable) {
            // False means do NOT convert keys over
            return array_values(iterator_to_array($array, false));
        }

        throw new InvalidArgumentException('TaskIds for pipeline must be an array or iterable object');
    }
}