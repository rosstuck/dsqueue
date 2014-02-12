<?php
namespace DS\Worker;

use DS\Queue\Consumer\Consumer;
use DS\Queue\Job\Job;
use DS\Queue\Queue;
use DS\Worker\Event\JobCompletedEvent;
use DS\Worker\Event\NoPendingJobEvent;
use DS\Worker\Event\PassCompletedEvent;
use DS\Worker\Event\ResetEvent;
use DS\Worker\Event\ShutdownEvent;
use DS\Worker\Exception\UnknownQueueStatus;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Coordinates moving through the queue in a flexible fashion
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class Worker
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Begin processing the backlog of jobs in the queue
     *
     * @param Queue $queue
     * @param Consumer $consumer
     * @throws Exception\UnknownQueueStatus
     */
    public function work(Queue $queue, Consumer $consumer)
    {
        $this->eventDispatcher->dispatch(ResetEvent::NAME, new ResetEvent());

        do {
            $result = $queue->processNextJob($consumer);

            // Handle the known return values
            if ($result === Queue::RESULT_NO_JOB) {
                $this->eventDispatcher->dispatch(NoPendingJobEvent::NAME, new NoPendingJobEvent());
            } elseif (is_object($result) && $result instanceof Job) {
                $this->eventDispatcher->dispatch(JobCompletedEvent::NAME, new JobCompletedEvent($result, $queue));
            } else {
                throw new UnknownQueueStatus("Expected job or status code, received '{$result}'");
            }

            // Fire the pass completed event, this gives us a chance to exit
            $passCompletedEvent = new PassCompletedEvent();
            $this->eventDispatcher->dispatch(PassCompletedEvent::NAME, $passCompletedEvent);
        } while(!$passCompletedEvent->isTerminating());

        $this->eventDispatcher->dispatch(ShutdownEvent::NAME, new ShutdownEvent());
    }
}