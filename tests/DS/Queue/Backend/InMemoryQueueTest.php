<?php

namespace DS\Queue\Backend;

use DS\Mock\MockJob;
use DS\Queue\Consumer\Consumer;
use DS\Queue\Queue;
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
     * @var Mock|Consumer
     */
    protected $consumerMock;

    protected function setUp()
    {
        $this->queue = new InMemoryQueue();

        $this->consumerMock = \Mockery::mock('DS\Queue\Consumer\Consumer');
        $this->consumerMock->shouldReceive('getAvailableTaskIds')->andReturn(['foo', 'bar']);
    }

    public function testCallsConsumerOnJobsInCorrectOrder()
    {
        $job1 = new MockJob('foo');
        $job2 = new MockJob('bar');

        $this->queue->queue($job1);
        $this->queue->queue($job2);

        $this->consumerMock->shouldReceive('execute')->once()->with($job1);
        $this->consumerMock->shouldReceive('execute')->once()->with($job2);
        $result1 = $this->queue->processNextJob($this->consumerMock);
        $result2 = $this->queue->processNextJob($this->consumerMock);

        $this->assertSame($job1, $result1);
        $this->assertSame($job2, $result2);
    }

    public function testEmptyQueueReturnsNoPendingJobs()
    {
        $this->consumerMock->shouldReceive('execute')->never();
        $result = $this->queue->processNextJob($this->consumerMock);

        $this->assertEquals(Queue::RESULT_NO_JOB, $result);
    }

    public function testConsumerOnlyReceivesJobsItHasTasksFor()
    {
        $this->queue->queue(new MockJob('some_unknown_task'));
        $this->assertEquals(Queue::RESULT_NO_JOB, $this->queue->processNextJob($this->consumerMock));
    }

    // This might seem over the top but I'm looking for leftover state issues
    // Consider it an integration test, if you prefer.
    public function testEmptyingTheQueueAndRefilling()
    {
        $job1 = new MockJob('foo');
        $job2 = new MockJob('foo');
        $this->consumerMock->shouldReceive('execute');

        // Empty the queue
        $this->queue->queue($job1);
        $result1 = $this->queue->processNextJob($this->consumerMock);
        $result2 = $this->queue->processNextJob($this->consumerMock);
        // Add one and do it again
        $this->queue->queue($job2);
        $result3 = $this->queue->processNextJob($this->consumerMock);
        $result4 = $this->queue->processNextJob($this->consumerMock);

        $this->assertSame($job1, $result1);
        $this->assertSame(Queue::RESULT_NO_JOB, $result2);
        $this->assertSame($job2, $result3);
        $this->assertSame(Queue::RESULT_NO_JOB, $result4);
    }
}
 