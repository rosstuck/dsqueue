<?php

namespace DS\Worker;

use DS\Mock\MockJob;
use DS\Queue\Backend\InMemoryQueue;
use DS\Queue\Consumer\Consumer;
use DS\Queue\Queue;
use DS\Worker\Event\PassCompletedEvent;
use DS\Mock\MockEventDispatcher;
use Mockery;

/**
 * Testing the worker is mostly about communication between objects.
 *
 * We want to verify that the queue and consumer are both being triggered but
 * also that the correct events are being dispatched as this is critical for
 * all the plugins that are running.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class WorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockEventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var Consumer
     */
    protected $consumer;

    /**
     * @var int
     */
    protected $numberOfPasses;

    /**
     * @var int
     */
    protected $allowedNumberOfPasses;

    protected function setUp()
    {
        // Setup our worker with an event dispatcher that lets us inspect
        // all events that were fired during execution
        $this->eventDispatcher = new MockEventDispatcher();
        $this->worker = new Worker($this->eventDispatcher);

        // Rig the event dispatcher to always exit after one iteration
        $this->numberOfPasses = 0;
        $this->allowedNumberOfPasses = 1;
        $this->eventDispatcher->addListener(
            PassCompletedEvent::NAME,
            function(PassCompletedEvent $event) {
                $this->numberOfPasses++;
                if ($this->numberOfPasses >= $this->allowedNumberOfPasses) {
                    $event->terminateWorker();
                }

            }
        );

        // Basic queue for testing
        $this->queue = new InMemoryQueue();

        // Setup a consumer that knows "foo" and "bar" tasks but does nothing else
        $this->consumer = Mockery::mock('DS\Queue\Consumer\Consumer');
        $this->consumer->shouldReceive('getAvailableTaskIds')->andReturn(['foo', 'bar']);
        $this->consumer->shouldReceive('execute');

    }

    public function testEmptyQueueThrowsProperEvents()
    {
        $this->worker->work($this->queue, $this->consumer);

        $events = $this->eventDispatcher->dequeueEvents();
        $this->assertEventOrder(['Reset', 'NoPendingJob', 'PassCompleted', 'Shutdown'], $events);
    }

    public function testWorkerProcessesJobs()
    {
        $job1 = new MockJob('foo');
        $this->queue->queue($job1);

        $this->worker->work($this->queue, $this->consumer);

        $events = $this->eventDispatcher->dequeueEvents();
        $this->assertEventOrder(['Reset', 'JobCompleted', 'PassCompleted', 'Shutdown'], $events);
        $this->assertSame($job1, $events[1]->getJob());
    }

    public function testWorkerContinuesWithoutJobs()
    {
        $this->allowedNumberOfPasses = 4;

        $this->queue->queue(new MockJob('foo'));
        $this->queue->queue(new MockJob('bar'));

        $this->worker->work($this->queue, $this->consumer);

        $events = $this->eventDispatcher->dequeueEvents();
        $this->assertEventOrder(
            //         job1                             job2                             third pass, no job              fourth pass, no job
            ['Reset', 'JobCompleted', 'PassCompleted', 'JobCompleted', 'PassCompleted', 'NoPendingJob', 'PassCompleted', 'NoPendingJob', 'PassCompleted', 'Shutdown'],
            $events
        );
    }

    protected function assertEventOrder($eventNames, $events)
    {
        $this->assertCount(count($eventNames), $events, 'Event count did not match up');

        foreach($events as $index => $event) {
            $this->assertInstanceOf('DS\\Worker\\Event\\'.$eventNames[$index].'Event', $event);
        }
    }
}
 