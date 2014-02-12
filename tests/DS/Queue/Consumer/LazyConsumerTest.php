<?php

namespace DS\Queue\Consumer;

use DS\Mock\MockJob;

/**
 * @author Ross Tuck <me@rosstuck.com>
 */
class LazyConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Pimple
     */
    protected $container;

    /**
     * @var LazyConsumer
     */
    protected $consumer;

    protected function setUp()
    {
        $this->container = new \Pimple();

        // Setup a simple DI container with a "foo" and "bar" task
        $this->container['task_foo'] = $this->container->share(
            function () {
                return \Mockery::mock('DS\Queue\Task\Task');
            }
        );
        $this->container['task_bar'] = $this->container->share(
            function () {
                return \Mockery::mock('DS\Queue\Task\Task');
            }
        );
        $this->consumer = new LazyConsumer($this->container);
        $this->consumer->registerTask('foo', 'task_foo');
        $this->consumer->registerTask('bar', 'task_bar');
    }

    public function testContainerListsAllTasks()
    {
        $this->assertEquals(['foo', 'bar'], $this->consumer->getAvailableTaskIds());
    }

    public function testEmptyContainerHasNoTasks()
    {
        $consumer = new LazyConsumer(new \Pimple());
        $this->assertEquals([], $consumer->getAvailableTaskIds());
    }

    public function testMatchesTaskToId()
    {
        $this->container['task_foo']->shouldReceive('execute')->once();

        $this->consumer->execute(new MockJob('foo'));
    }

    /**
     * @expectedException \DS\Queue\Exception\UnknownTaskException
     */
    public function testUnknownTaskThrowsException()
    {
        $this->consumer->execute(new MockJob('some_unknown_task'));
    }

    /**
     * @expectedException \DS\Queue\Exception\InvalidTaskException
     */
    public function testInvalidTaskObjectGeneratesError()
    {
        $this->container['not_a_task'] = $this->container->share(
            function () {
                return new \DateTime('now');
            }
        );
        $this->consumer->registerTask('task_with_fake_service', 'not_a_task');
        $this->consumer->execute(new MockJob('task_with_fake_service'));
    }
}