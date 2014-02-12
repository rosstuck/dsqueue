<?php

namespace DS\Demo\Task;

use DS\Queue\Job\Job;
use DS\Queue\Task\Task;
use Psr\Log\LoggerInterface;

/**
 * Output content to log for easy debugging
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class LogContentTask implements Task
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Job $job
     * @return void
     */
    public function execute(Job $job)
    {
        $this->logger->debug('=======> Content is now: ' . $job->getContent());
    }
}