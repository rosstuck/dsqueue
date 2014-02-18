<?php

namespace DS\Queue;

use DS\Queue\Job\Job;
use DS\Queue\Task\Task;

/**
 * Stores Jobs in a...well, queue and then processes them using a Consumer
 *
 * A traditional queue interface uses push/pop. Here, the queue() function is
 * pretty much the same as push. However, processNextJob() works more like a
 * Visitor pattern. The idea is that you pass in a consumer which has a list of
 * Tasks it knows how to perform and can also tell you what tasks those are by
 * returning the Task Ids. The queue uses this information to look up a matching
 * Job (by comparing its Task Id).
 *
 * There's a few benefits to this but the big one is that this is much easier
 * to abstract for different queue backends. Some (*cough*Gearman*cough*) use
 * callback systems that want to do the execution internally, while others have
 * special handling for updating remote statues or removing them from queues.
 * Since each queue backend is responsible for invoking the actual execution of
 * the task, it's really easy to add extra handling around it.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
interface Queue {

    /**
     * Status code returned when no processable jobs were waiting in the queue
     * @var string
     */
    const RESULT_NO_JOB = 'no_job';

    /**
     * Status code returned when a job is completed in the queu
     */
    const RESULT_JOB_COMPLETE = 'job_complete';

    /**
     * Add a job to the queue
     *
     * @param Job $job
     */
    public function queue(Job $job);

    /**
     * Complete a job in the queue using the given consumer.
     *
     * Only Jobs that fall within the consumer's registered tasks will be
     * processed, everything else will remain in the queue. On completion, the
     * processed job or an appropriate result code will be returned.
     *
     * @param Task $task
     * @return Job|string
     */
    public function processNextJob(Task $task);
}