<?php

namespace DS\Queue\Job;

use DS\Mock\MockSequentialJob;

/**
 * @author Ross Tuck <me@rosstuck.com>
 */
class SequentialJobWithPipelineTest extends \PHPUnit_Framework_TestCase
{
    public function testTasksAppearInSequence()
    {
        $job = new MockSequentialJob(['foo', 'bar', 'baz']);

        $this->assertFalse($job->isComplete());
        $this->assertEquals('foo', $job->getTaskId());

        $job->advanceToNextTask();
        $this->assertFalse($job->isComplete());
        $this->assertEquals('bar', $job->getTaskId());

        $job->advanceToNextTask();
        $this->assertTrue($job->isComplete());
        $this->assertEquals('baz', $job->getTaskId());
    }

    public function testEmptyPipelineIsComplete()
    {
        $job = new MockSequentialJob([]);

        $this->assertTrue($job->isComplete());
        $this->assertNull($job->getTaskId());
    }
}
 