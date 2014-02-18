<?php

namespace DS\Demo\Task;

use DS\Queue\Job\Job;
use DS\Queue\Job\StandardJob;
use DS\Queue\Queue;
use DS\Queue\Task\Task;
use JMS\Serializer\SerializerInterface;

/**
 * @author Ross Tuck <me@rosstuck.com>
 */
class ReadBitlyTask implements Task
{
    /**
     * @var \DS\Queue\Queue
     */
    protected $queue;

    /**
     * @var \JMS\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @param Queue The queue to republish to
     */
    public function __construct(Queue $queue, SerializerInterface $serializer)
    {
        $this->queue = $queue;
        $this->serializer = $serializer;
    }

    /**
     * Perform some operation using the Job
     *
     * @param Job $job
     * @return void
     */
    public function execute(Job $job)
    {
        $link = $this->serializer->deserialize($job->getPayload(), 'DS\Demo\Entity\BitlyLink', 'json');
        $this->queue->queue(new StandardJob($link));
    }
}