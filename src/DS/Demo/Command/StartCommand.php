<?php

namespace DS\Demo\Command;

use Cilex\Command\Command;
use DS\Demo\Job\FormatContentJob;
use DS\Queue\Queue;
use DS\Queue\Task\Task;
use DS\Worker\Worker;
use Symfony\Component\Console\Input\InputArgument;
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
     * @var \Pimple
     */
    protected $container;

    /**
     * @param null|string $name
     * @param Worker $worker
     * @param Pimple $container
     */
    public function __construct($name, Worker $worker, \Pimple $container)
    {
        parent::__construct($name);

        $this->worker = $worker;
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('worker:run')
            ->setDescription('Start running our worker!')
            ->addArgument('queue', InputArgument::REQUIRED, 'ID of queue to run (redis or http)')
            ->addArgument('task', InputArgument::REQUIRED, 'ID of task to run (bitly, twitter, catify)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $this->getQueue($input->getArgument('queue'));
        $task = $this->getTask($input->getArgument('task'));

        // Start it up!
        $this->worker->work($queue, $task);
    }

    /**
     * This is really really ugly and I would normally refactor this to a queue service locator or some such.
     *
     * Just for demo. Promise.
     *
     * @param string $queueId
     * @return Queue
     */
    protected function getQueue($queueId)
    {
        return $this->container['ds.queue.'.$queueId];
    }

    /**
     * @param string $taskId
     * @return Task
     */
    protected function getTask($taskId)
    {
        return $this->container['ds.task.'.$taskId];
    }
}