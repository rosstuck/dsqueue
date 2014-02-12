<?php

namespace DS\Queue\Exception;

/**
 * Thrown when an consumer tries to execute an object that is not a Task
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class InvalidTaskException extends \Exception
{
}