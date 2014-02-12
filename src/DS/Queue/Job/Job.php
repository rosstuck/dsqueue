<?php

namespace DS\Queue\Job;

/**
 * Jobs are requests to perform a specific task.
 *
 * They are typically small Data Transfer Objects that contain the information
 * Tasks need to execute. The only requirement for a job is that it must know
 * which task should be executed on it.
 *
 * Otherwise, it is recommended that Jobs be easy to serialize and have few
 * dependencies, as most queue backends will need to pass them over the wire.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
interface Job
{
    public function getTaskId();
}