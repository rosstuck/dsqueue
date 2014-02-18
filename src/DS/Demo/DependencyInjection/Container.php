<?php

namespace DS\Demo\DependencyInjection;

use Doctrine\Common\Annotations\AnnotationRegistry;
use DS\Demo\Task\CatifyTask;
use DS\Demo\Task\LogContentTask;
use DS\Demo\Task\ReadBitlyTask;
use DS\Demo\Task\ReverseStringTask;
use DS\Demo\Command\StartCommand;
use DS\Queue\Backend\InMemoryQueue;
use DS\Queue\Backend\RedisQueue;
use DS\Worker\Plugin\BackOffPlugin;
use DS\Worker\Plugin\GracefulShutdownPlugin;
use DS\Worker\Plugin\LoggingPlugin;
use DS\Worker\Plugin\SequentialJobPlugin;
use DS\Worker\Worker;
use JMS\Serializer\SerializerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Redis;

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
        // Necessary to wireup JMS Serializer's annotation reader
        AnnotationRegistry::registerLoader('class_exists');

        $this['ds.worker.event_dispatcher'] = $this->share(
            function ($app) {
                $dispatcher = new EventDispatcher();

                // All the plugins for our Worker
                $dispatcher->addSubscriber(new BackOffPlugin(1));
                $dispatcher->addSubscriber(new LoggingPlugin($app['ds.logger']));
                //$dispatcher->addSubscriber(new SequentialJobPlugin());
                $dispatcher->addSubscriber(new GracefulShutdownPlugin($app['ds.logger']));

                return $dispatcher;
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

        $this['ds.queue.locator'] = $this->share(
            function () {
                return new InMemoryQueue();
            }
        );

        $this['ds.queue.in_memory'] = $this->share(
            function () {
                return new InMemoryQueue();
            }
        );

        $this['ds.redis'] = $this->share(
            function () {
                $redis = new Redis();
                $redis->pconnect('localhost');
                return $redis;
            }
        );

        $this['ds.queue.bitly_incoming'] = $this->share(
            function ($app) {
                return new RedisQueue($app['ds.redis'], 'bitly');
            }
        );

        $this['ds.queue.redis_output'] = $this->share(
            function ($app) {
                return new RedisQueue($app['ds.redis'], 'output');
            }
        );

        $this['ds.queue.http_stream'] = $this->share(
            function () {
                // TODO
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
                    $app
                );
            }
        );

        $this['ds.serializer'] = $this->share(
            function () {
                return SerializerBuilder::create()->build();
            }
        );

        // Boilerplate for the tasks to show off lazy loading
        $this['ds.task.reverse_string'] = $this->share(
            function () {
                return new ReverseStringTask();
            }
        );

        $this['ds.task.catify'] = $this->share(
            function ($app) {
                return new CatifyTask($app['ds.logger']);
            }
        );

        $this['ds.task.read_bitly'] = $this->share(
            function ($app) {
                return new ReadBitlyTask($app['ds.queue.redis_output'], $app['ds.serializer']);
            }
        );

        $this['ds.task.log_content'] = $this->share(
            function ($app) {
                return new LogContentTask($app['ds.logger']);
            }
        );
    }
}