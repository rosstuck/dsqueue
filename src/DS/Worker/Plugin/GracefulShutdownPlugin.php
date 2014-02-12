<?php

namespace DS\Worker\Plugin;

use DS\Worker\Event\PassCompletedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Gracefully exits if receiving a SIGTERM or SIGINT signal
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class GracefulShutdownPlugin implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    protected $shouldTerminate;

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

        pcntl_signal(SIGINT, [$this, 'checkSignal']);
        pcntl_signal(SIGTERM, [$this, 'checkSignal']);
    }

    /**
     * Callback function for posix signals
     */
    public function checkSignal()
    {
        $this->logger->info('Received exit signal');
        $this->shouldTerminate = true;
    }

    /**
     * Invoked on each pass of the worker, checking if we should terminate now
     *
     * @param PassCompletedEvent $event
     */
    public function checkForExitSignal(PassCompletedEvent $event)
    {
        // Releases any pending signals, thus invoking checkSignal()
        pcntl_signal_dispatch();

        // If we want to stop the worker, we can indicate this through the event
        if ($this->shouldTerminate) {
            $this->logger->info('Worker marked for exit');
            $event->terminateWorker();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [PassCompletedEvent::NAME => 'checkForExitSignal'];
    }
}