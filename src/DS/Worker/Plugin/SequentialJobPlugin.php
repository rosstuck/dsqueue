<?php

namespace DS\Worker\Plugin;

use DS\Queue\Job\SequentialJob;
use DS\Worker\Event\JobCompletedEvent;
use DS\Worker\Event\NoPendingJobEvent;
use DS\Worker\Event\PassCompletedEvent;
use DS\Worker\Event\ResetEvent;
use DS\Worker\Event\ShutdownEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds support for automatically moving through job pipelines
 *
 * @see SequentialJob
 * @author Ross Tuck <me@rosstuck.com>
 */
class SequentialJobPlugin implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [JobCompletedEvent::NAME => 'onJobCompleted'];
    }

    /**
     * When a job finishes, check if it needs to be requeued for another task
     *
     * @param JobCompletedEvent $event
     */
    public function onJobCompleted(JobCompletedEvent $event)
    {
        $job = $event->getJob();

        // Advance the job's pipeline and re-queue it for next time.
        if ($job instanceof SequentialJob && !$job->isComplete()) {
            $job->advanceToNextTask();
            $event->getQueue()->queue($job);
        }
    }
}