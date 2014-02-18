<?php

namespace DS\Demo\Task;

use DS\Queue\Job\Job;
use DS\Queue\Task\Task;
use Psr\Log\LoggerInterface;

/**
 * Adds cat emoticons. I mean, why not?
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class CatifyTask implements Task
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
        $this->logger->debug('=^.^= ' . $job->getPayload() . ' =^.^=');
    }
}