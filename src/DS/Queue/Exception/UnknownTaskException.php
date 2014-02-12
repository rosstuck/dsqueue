<?php

namespace DS\Queue\Exception;

/**
 * Thrown when a task can not be resolved to an object, usually an invalid id
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class UnknownTaskException extends \Exception
{
}