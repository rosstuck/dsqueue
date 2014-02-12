<?php

namespace DS\Worker\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Indicates the worker is now stopping forever
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class ShutdownEvent extends Event
{
    const NAME = 'worker.shutdown';
}