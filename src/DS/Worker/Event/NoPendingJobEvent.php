<?php

namespace DS\Worker\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Indicates that the queue had no jobs to dispatch in this pass
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class NoPendingJobEvent extends Event
{
    const NAME = 'worker.no_pending_job';
}