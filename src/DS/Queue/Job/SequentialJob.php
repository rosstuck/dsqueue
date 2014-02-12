<?php

namespace DS\Queue\Job;

/**
 * A SequentialJob is a Job that requires multiple tasks to be run on it
 *
 * In some scenarios, a Job might be part of a pipeline: a series of composed
 * tasks that produce a greater outcome. To support this, the only requirement
 * is that the job change the output of its getTaskId() function when the
 * previous job is complete and then simply requeue the job.
 *
 * This interface makes that process a bit simpler: a Job can now be told when
 * the previous task completed successfully and it can indicate when it's
 * reached the end of its tasks. The entire process can be automated further
 * by enabling the SequentialJobPlugin which will check the progress of a job
 * and then requeue it automatically at the end of a pass.
 *
 * The SequentialJob extension is a bit experimental and it's not the only way
 * to create pipelines but it's simple and makes for a good example. It's also
 * very easy to track the progress of a single job, monitor the queue (since it
 * requeues the job, you can use all the same monitoring mechanisms as for
 * incoming regular jobs).
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
interface SequentialJob extends Job
{
    /**
     * Inform the job to begin returning the next Task in its pipeline.
     *
     * @return void
     */
    public function advanceToNextTask();

    /**
     * Indicates if there are more tasks for this job, needed for requeueing
     *
     * @return bool
     */
    public function isComplete();
}