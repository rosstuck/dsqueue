<?php

namespace DS\Worker\Plugin;

use DS\Worker\Event\JobCompletedEvent;
use DS\Worker\Event\NoPendingJobEvent;
use DS\Worker\Event\PassCompletedEvent;
use DS\Worker\Event\ResetEvent;
use DS\Worker\Event\ShutdownEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handy-dandy logger plugin for development.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class LoggingPlugin implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ResetEvent::NAME => 'onReset',
            ShutdownEvent::NAME => 'onShutdown',
            NoPendingJobEvent::NAME => 'onNoPendingJob',
            JobCompletedEvent::NAME => 'onJobCompleted',
            PassCompletedEvent::NAME => 'onPassCompleted'
        ];
    }

    public function onReset()
    {
        $this->logger->info('Resetting worker...');
    }

    public function onShutdown()
    {
        $this->logger->info('Shutting worker down...');
    }

    public function onNoPendingJob()
    {
        $this->logger->debug('No pending jobs.');
    }

    public function onJobCompleted(JobCompletedEvent $event)
    {
        $this->logger->debug('Finished job. Task was: '.$event->getJob()->getTaskId());
    }

    public function onPassCompleted()
    {
        $this->logger->debug('Pass completed');
    }
}