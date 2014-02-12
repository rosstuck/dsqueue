<?php

namespace DS\Worker\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Worker is starting up or completely resetting state
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class ResetEvent extends Event
{
    const NAME = 'worker.pending';
}