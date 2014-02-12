<?php

namespace DS\Demo\Command;

use Cilex\Command\Command;
use DS\Demo\Job\FormatContentJob;
use DS\Queue\Consumer\Consumer;
use DS\Queue\Queue;
use DS\Worker\Worker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Simple kickstarter command for a worker
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class StartCommand extends Command
{
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
     * @param null|string $name
     * @param Worker $worker
     * @param Queue $queue
     * @param Consumer $consumer
     */
    public function __construct($name, Worker $worker, Queue $queue, Consumer $consumer)
    {
        parent::__construct($name);

        $this->worker = $worker;
        $this->queue = $queue;
        $this->consumer = $consumer;
    }

    protected function configure()
    {
        $this
            ->setName('worker:run')
            ->setDescription('Start running our worker!');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Let's add a fake job to our queue
        $job = new FormatContentJob('lorem ipsum', ['reverse_string', 'catify', 'log_content']);
        $this->queue->queue($job);

        // Start it up!
        $this->worker->work($this->queue, $this->consumer);
    }
}