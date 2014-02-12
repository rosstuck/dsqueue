<?php

namespace DS\Demo\DependencyInjection;

use DS\Demo\Task\CatifyTask;
use DS\Demo\Task\LogContentTask;
use DS\Demo\Task\ReverseStringTask;
use DS\Demo\Command\StartCommand;
use DS\Queue\Backend\InMemoryQueue;
use DS\Queue\Consumer\LazyConsumer;
use DS\Worker\Plugin\BackOffPlugin;
use DS\Worker\Plugin\GracefulShutdownPlugin;
use DS\Worker\Plugin\LoggingPlugin;
use DS\Worker\Plugin\SequentialJobPlugin;
use DS\Worker\Worker;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * A simple DI container for our test app.
 *
 * Here you can tweak the plugins, the logger, the tasks available, etc. In a
 * real app, you'd probably have some deeper integration with your framework's
 * DI container.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class Container extends \Pimple
{
    public function __construct()
    {
        $this['ds.worker.event_dispatcher'] = $this->share(
            function ($app) {
                $dispatcher = new EventDispatcher();

                // All the plugins for our Worker
                $dispatcher->addSubscriber(new BackOffPlugin(1));
                $dispatcher->addSubscriber(new LoggingPlugin($app['ds.logger']));
                $dispatcher->addSubscriber(new SequentialJobPlugin());
                $dispatcher->addSubscriber(new GracefulShutdownPlugin($app['ds.logger']));

                return $dispatcher;
            }
        );

        $this['ds.worker.consumer'] = $this->share(
            function ($app) {
                $consumer = new LazyConsumer($app);
                // Register our tasks: first param is taskId, second is DI id
                // See the bottom of the file for the definitions
                $consumer->registerTask('catify', 'ds.task.catify');
                $consumer->registerTask('log_content', 'ds.task.log_content');
                $consumer->registerTask('reverse_string', 'ds.task.reverse_string');

                return $consumer;
            }
        );

        $this['ds.logger'] = $this->share(
            function () {
                // Logger configured to echo all output
                $logger = new Logger('main');
                $logger->pushHandler(new StreamHandler("php://output"));

                return $logger;
            }
        );

        $this['ds.worker.queue'] = $this->share(
            function () {
                return new InMemoryQueue();
            }
        );

        $this['ds.worker.worker'] = $this->share(
            function ($app) {
                return new Worker($app['ds.worker.event_dispatcher']);
            }
        );

        $this['ds.worker.command.start'] = $this->share(
            function ($app) {
                return new StartCommand(
                    null,
                    $app['ds.worker.worker'],
                    $app['ds.worker.queue'],
                    $app['ds.worker.consumer']
                );
            }
        );

        // Boilerplate for the tasks to show off lazy loading
        $this['ds.task.reverse_string'] = $this->share(
            function () {
                return new ReverseStringTask();
            }
        );

        $this['ds.task.catify'] = $this->share(
            function () {
                return new CatifyTask();
            }
        );

        $this['ds.task.log_content'] = $this->share(
            function ($app) {
                return new LogContentTask($app['ds.logger']);
            }
        );
    }
}