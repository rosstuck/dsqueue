<?php

namespace DS\Worker\Exception;

/**
 * Thrown whenever the queue returns an invalid status code or job
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class UnknownQueueStatus extends \Exception
{
}