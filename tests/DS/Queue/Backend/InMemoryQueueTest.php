<?php

namespace DS\Queue\Backend;

use DS\Mock\MockJob;
use DS\Queue\Queue;
use DS\Queue\Task\Task;
use Mockery\Mock;

/**
 * Basic tests for our InMemoryQueue
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class InMemoryQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryQueue
     */
    protected $queue;

    /**
     * @var Mock|Task
     */
    protected $taskMock;

    protected function setUp()
    {
        $this->queue = new InMemoryQueue();

        $this->taskMock = \Mockery::mock('DS\Queue\Task\Task');
    }

    public function testCallsConsumerOnJobsInCorrectOrder()
    {
        $job1 = new MockJob('foo');
        $job2 = new MockJob('bar');

        $this->queue->queue($job1);
        $this->queue->queue($job2);

        $this->taskMock->shouldReceive('execute')->once()->with($job1);
        $this->taskMock->shouldReceive('execute')->once()->with($job2);
        $result1 = $this->queue->processNextJob($this->taskMock);
        $result2 = $this->queue->processNextJob($this->taskMock);

        $this->assertSame(Queue::RESULT_JOB_COMPLETE, $result1);
        $this->assertSame(Queue::RESULT_JOB_COMPLETE, $result2);
    }

    public function testEmptyQueueReturnsNoPendingJobs()
    {
        $this->taskMock->shouldReceive('execute')->never();
        $result = $this->queue->processNextJob($this->taskMock);

        $this->assertEquals(Queue::RESULT_NO_JOB, $result);
    }

    // This might seem over the top but I'm looking for leftover state issues
    // Consider it an integration test, if you prefer.
    public function testEmptyingTheQueueAndRefilling()
    {
        $job1 = new MockJob('foo');
        $job2 = new MockJob('foo');
        $this->taskMock->shouldReceive('execute');

        // Empty the queue
        $this->queue->queue($job1);
        $result1 = $this->queue->processNextJob($this->taskMock);
        $result2 = $this->queue->processNextJob($this->taskMock);
        // Add one and do it again
        $this->queue->queue($job2);
        $result3 = $this->queue->processNextJob($this->taskMock);
        $result4 = $this->queue->processNextJob($this->taskMock);

        $this->assertSame(Queue::RESULT_JOB_COMPLETE, $result1);
        $this->assertSame(Queue::RESULT_NO_JOB, $result2);
        $this->assertSame(Queue::RESULT_JOB_COMPLETE, $result3);
        $this->assertSame(Queue::RESULT_NO_JOB, $result4);
    }
}
 