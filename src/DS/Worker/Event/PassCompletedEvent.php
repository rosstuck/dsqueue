<?php

namespace DS\Worker\Event;

use DS\Worker\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatched at the end of the job polling loop, regardless of outcome
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class PassCompletedEvent extends Event
{
    const NAME = 'worker.pass_completed';

    /**
     * @var bool
     */
    protected $shouldTerminate;

    /**
     * Should the worker terminate?
     *
     * @param bool $flag
     * @throws \DS\Worker\Exception\InvalidArgumentException
     */
    public function terminateWorker($flag = true)
    {
        if (!is_bool($flag)) {
            throw new InvalidArgumentException('Termination flag must be a boolean');
        }

        $this->shouldTerminate = $flag;
    }

    /**
     * Should the worker shut down now?
     *
     * @return bool
     */
    public function isTerminating()
    {
        return $this->shouldTerminate;
    }
}